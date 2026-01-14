<?php

namespace App\Services;

use App\Models\Template;
use App\Models\TemplateVariable;
use App\Models\TemplateVersion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Exception;

class TemplateService
{
    /**
     * Create a new template
     */
    public function create(array $data): Template
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['created_by'] = Auth::id();
        
        $template = Template::create($data);
        
        // Create initial version
        $this->createVersion($template, $data['content'], 'Initial version');
        
        return $template->fresh('versions');
    }

    /**
     * Update an existing template
     */
    public function update(Template $template, array $data): Template
    {
        $oldContent = $template->content;
        
        $template->update($data);
        
        // Create new version if content changed
        if (isset($data['content']) && $data['content'] !== $oldContent) {
            $this->createVersion($template, $data['content'], 'Content updated');
        }
        
        return $template->fresh('versions');
    }

    /**
     * Delete a template (soft delete)
     */
    public function delete(Template $template): bool
    {
        if (!$template->canBeDeleted()) {
            throw new Exception('Cannot delete template that is set as default');
        }
        
        return $template->delete();
    }

    /**
     * Render template with variables
     */
    public function render(Template $template, array $variables): string
    {
        $content = $template->content;
        
        // Replace variables in format {{variable_name}}
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Create a new version of the template
     */
    public function createVersion(Template $template, string $content, string $changeDescription = null): TemplateVersion
    {
        $latestVersion = $template->versions()->latest('version_number')->first();
        $versionNumber = $latestVersion ? $latestVersion->version_number + 1 : 1;
        
        return $template->versions()->create([
            'version_number' => $versionNumber,
            'content' => $content,
            'change_description' => $changeDescription,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Rollback template to a specific version
     */
    public function rollbackToVersion(Template $template, int $versionNumber): Template
    {
        $version = $template->versions()
            ->where('version_number', $versionNumber)
            ->firstOrFail();
        
        $template->update(['content' => $version->content]);
        
        // Create new version record for the rollback
        $this->createVersion(
            $template, 
            $version->content, 
            "Rolled back to version {$versionNumber}"
        );
        
        return $template->fresh('versions');
    }

    /**
     * Get available variables for a template type
     */
    public function getAvailableVariables(string $templateType): array
    {
        return TemplateVariable::where(function ($query) use ($templateType) {
            $query->whereJsonContains('applicable_to', $templateType)
                  ->orWhereNull('applicable_to');
        })
        ->get()
        ->map(function ($variable) {
            return [
                'name' => $variable->name,
                'placeholder' => '{{' . $variable->name . '}}',
                'description' => $variable->description,
                'type' => $variable->type,
                'example' => $variable->example_value,
            ];
        })
        ->toArray();
    }

    /**
     * Preview template with example data
     */
    public function preview(Template $template): string
    {
        $variables = $this->getAvailableVariables($template->type);
        
        $exampleData = [];
        foreach ($variables as $variable) {
            $exampleData[$variable['name']] = $variable['example'] ?? '[' . $variable['name'] . ']';
        }
        
        return $this->render($template, $exampleData);
    }

    /**
     * Set template as default for its type
     */
    public function setAsDefault(Template $template): Template
    {
        // Remove default flag from other templates of the same type
        Template::where('type', $template->type)
            ->where('id', '!=', $template->id)
            ->update(['is_default' => false]);
        
        $template->update(['is_default' => true]);
        
        return $template;
    }

    /**
     * Get template by slug
     */
    public function getBySlug(string $slug): ?Template
    {
        return Template::where('slug', $slug)->first();
    }

    /**
     * Get default template for a type
     */
    public function getDefaultForType(string $type): ?Template
    {
        return Template::active()
            ->where('type', $type)
            ->where('is_default', true)
            ->first();
    }
}
