<?php

namespace App\Services;

use Exception;

class TemplateBuilderService
{
    /**
     * Available template components
     */
    private array $availableComponents = [
        'text' => [
            'type' => 'text',
            'properties' => ['content', 'fontSize', 'fontWeight', 'color', 'align', 'margin', 'padding'],
        ],
        'heading' => [
            'type' => 'heading',
            'properties' => ['content', 'level', 'color', 'align', 'margin'],
        ],
        'image' => [
            'type' => 'image',
            'properties' => ['src', 'alt', 'width', 'height', 'align', 'margin'],
        ],
        'button' => [
            'type' => 'button',
            'properties' => ['text', 'url', 'backgroundColor', 'textColor', 'align', 'margin'],
        ],
        'table' => [
            'type' => 'table',
            'properties' => ['headers', 'rows', 'border', 'cellPadding', 'margin'],
        ],
        'divider' => [
            'type' => 'divider',
            'properties' => ['color', 'height', 'margin'],
        ],
        'spacer' => [
            'type' => 'spacer',
            'properties' => ['height'],
        ],
    ];

    /**
     * Build template from JSON structure
     */
    public function buildTemplate(array $structure): string
    {
        $html = $this->getHtmlWrapper();
        $bodyContent = '';
        
        foreach ($structure['components'] ?? [] as $component) {
            $bodyContent .= $this->renderComponent($component);
        }
        
        return str_replace('{{CONTENT}}', $bodyContent, $html);
    }

    /**
     * Render a single component to HTML
     */
    private function renderComponent(array $component): string
    {
        $type = $component['type'] ?? 'text';
        
        return match($type) {
            'text' => $this->renderText($component),
            'heading' => $this->renderHeading($component),
            'image' => $this->renderImage($component),
            'button' => $this->renderButton($component),
            'table' => $this->renderTable($component),
            'divider' => $this->renderDivider($component),
            'spacer' => $this->renderSpacer($component),
            default => '',
        };
    }

    /**
     * Render text component
     */
    private function renderText(array $component): string
    {
        $style = $this->buildStyle([
            'font-size' => $component['fontSize'] ?? '14px',
            'font-weight' => $component['fontWeight'] ?? 'normal',
            'color' => $component['color'] ?? '#000000',
            'text-align' => $component['align'] ?? 'left',
            'margin' => $component['margin'] ?? '10px 0',
            'padding' => $component['padding'] ?? '0',
        ]);
        
        $content = $component['content'] ?? '';
        
        return "<p style=\"{$style}\">{$content}</p>\n";
    }

    /**
     * Render heading component
     */
    private function renderHeading(array $component): string
    {
        $level = $component['level'] ?? 2;
        $style = $this->buildStyle([
            'color' => $component['color'] ?? '#000000',
            'text-align' => $component['align'] ?? 'left',
            'margin' => $component['margin'] ?? '20px 0 10px 0',
        ]);
        
        $content = $component['content'] ?? '';
        
        return "<h{$level} style=\"{$style}\">{$content}</h{$level}>\n";
    }

    /**
     * Render image component
     */
    private function renderImage(array $component): string
    {
        $src = $component['src'] ?? '';
        $alt = $component['alt'] ?? '';
        $width = $component['width'] ?? 'auto';
        $height = $component['height'] ?? 'auto';
        $align = $component['align'] ?? 'left';
        $margin = $component['margin'] ?? '10px 0';
        
        $style = $this->buildStyle([
            'width' => $width,
            'height' => $height,
            'display' => 'block',
            'margin' => $margin,
        ]);
        
        $wrapperStyle = "text-align: {$align};";
        
        return "<div style=\"{$wrapperStyle}\"><img src=\"{$src}\" alt=\"{$alt}\" style=\"{$style}\" /></div>\n";
    }

    /**
     * Render button component
     */
    private function renderButton(array $component): string
    {
        $text = $component['text'] ?? 'Button';
        $url = $component['url'] ?? '#';
        $bgColor = $component['backgroundColor'] ?? '#007bff';
        $textColor = $component['textColor'] ?? '#ffffff';
        $align = $component['align'] ?? 'left';
        $margin = $component['margin'] ?? '20px 0';
        
        $buttonStyle = $this->buildStyle([
            'display' => 'inline-block',
            'padding' => '12px 24px',
            'background-color' => $bgColor,
            'color' => $textColor,
            'text-decoration' => 'none',
            'border-radius' => '4px',
            'font-weight' => 'bold',
        ]);
        
        $wrapperStyle = $this->buildStyle([
            'text-align' => $align,
            'margin' => $margin,
        ]);
        
        return "<div style=\"{$wrapperStyle}\"><a href=\"{$url}\" style=\"{$buttonStyle}\">{$text}</a></div>\n";
    }

    /**
     * Render table component
     */
    private function renderTable(array $component): string
    {
        $headers = $component['headers'] ?? [];
        $rows = $component['rows'] ?? [];
        $border = $component['border'] ?? '1px solid #ddd';
        $cellPadding = $component['cellPadding'] ?? '8px';
        $margin = $component['margin'] ?? '20px 0';
        
        $tableStyle = $this->buildStyle([
            'width' => '100%',
            'border-collapse' => 'collapse',
            'margin' => $margin,
        ]);
        
        $cellStyle = $this->buildStyle([
            'border' => $border,
            'padding' => $cellPadding,
        ]);
        
        $html = "<table style=\"{$tableStyle}\">\n";
        
        // Headers
        if (!empty($headers)) {
            $html .= "<thead><tr>";
            foreach ($headers as $header) {
                $html .= "<th style=\"{$cellStyle}\">{$header}</th>";
            }
            $html .= "</tr></thead>\n";
        }
        
        // Rows
        $html .= "<tbody>";
        foreach ($rows as $row) {
            $html .= "<tr>";
            foreach ($row as $cell) {
                $html .= "<td style=\"{$cellStyle}\">{$cell}</td>";
            }
            $html .= "</tr>\n";
        }
        $html .= "</tbody></table>\n";
        
        return $html;
    }

    /**
     * Render divider component
     */
    private function renderDivider(array $component): string
    {
        $color = $component['color'] ?? '#dddddd';
        $height = $component['height'] ?? '1px';
        $margin = $component['margin'] ?? '20px 0';
        
        $style = $this->buildStyle([
            'border' => 'none',
            'border-top' => "{$height} solid {$color}",
            'margin' => $margin,
        ]);
        
        return "<hr style=\"{$style}\" />\n";
    }

    /**
     * Render spacer component
     */
    private function renderSpacer(array $component): string
    {
        $height = $component['height'] ?? '20px';
        
        $style = $this->buildStyle([
            'height' => $height,
            'display' => 'block',
        ]);
        
        return "<div style=\"{$style}\"></div>\n";
    }

    /**
     * Build CSS style string from array
     */
    private function buildStyle(array $properties): string
    {
        $styles = [];
        foreach ($properties as $key => $value) {
            if ($value !== null && $value !== '') {
                $styles[] = "{$key}: {$value}";
            }
        }
        return implode('; ', $styles);
    }

    /**
     * Get HTML wrapper for email
     */
    private function getHtmlWrapper(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="padding: 40px;">
                            {{CONTENT}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Get available components
     */
    public function getAvailableComponents(): array
    {
        return $this->availableComponents;
    }

    /**
     * Validate template structure
     */
    public function validateStructure(array $structure): array
    {
        $errors = [];
        
        if (!isset($structure['components']) || !is_array($structure['components'])) {
            $errors[] = 'Template must have a components array';
            return $errors;
        }
        
        foreach ($structure['components'] as $index => $component) {
            if (!isset($component['type'])) {
                $errors[] = "Component at index {$index} must have a type";
                continue;
            }
            
            if (!isset($this->availableComponents[$component['type']])) {
                $errors[] = "Unknown component type '{$component['type']}' at index {$index}";
            }
        }
        
        return $errors;
    }
}
