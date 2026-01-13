<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\SalesOrder;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class PdfService
{
    /**
     * Generate PDF for an invoice
     *
     * @param Invoice $invoice
     * @param string $template
     * @return string PDF content
     * @throws MpdfException
     */
    public function generateInvoicePdf(Invoice $invoice, string $template = 'default'): string
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
        ]);

        $html = $this->getInvoiceHtml($invoice, $template);
        
        $mpdf->WriteHTML($html);
        
        return $mpdf->Output('', 'S');
    }

    /**
     * Generate PDF for a quote
     *
     * @param Quote $quote
     * @param string $template
     * @return string PDF content
     * @throws MpdfException
     */
    public function generateQuotePdf(Quote $quote, string $template = 'default'): string
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
        ]);

        $html = $this->getQuoteHtml($quote, $template);
        
        $mpdf->WriteHTML($html);
        
        return $mpdf->Output('', 'S');
    }

    /**
     * Generate PDF for a sales order
     *
     * @param SalesOrder $salesOrder
     * @param string $template
     * @return string PDF content
     * @throws MpdfException
     */
    public function generateSalesOrderPdf(SalesOrder $salesOrder, string $template = 'default'): string
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
        ]);

        $html = $this->getSalesOrderHtml($salesOrder, $template);
        
        $mpdf->WriteHTML($html);
        
        return $mpdf->Output('', 'S');
    }

    /**
     * Get HTML content for invoice
     *
     * @param Invoice $invoice
     * @param string $template
     * @return string
     */
    protected function getInvoiceHtml(Invoice $invoice, string $template): string
    {
        $invoice->load(['client', 'items', 'amounts', 'numbering', 'status']);
        
        $templatePath = resource_path("views/pdf/invoices/{$template}.php");
        
        if (!file_exists($templatePath)) {
            $templatePath = resource_path('views/pdf/invoices/default.php');
        }
        
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }

    /**
     * Get HTML content for quote
     *
     * @param Quote $quote
     * @param string $template
     * @return string
     */
    protected function getQuoteHtml(Quote $quote, string $template): string
    {
        $quote->load(['client', 'user', 'status']);
        
        $templatePath = resource_path("views/pdf/quotes/{$template}.php");
        
        if (!file_exists($templatePath)) {
            $templatePath = resource_path('views/pdf/quotes/default.php');
        }
        
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }

    /**
     * Get HTML content for sales order
     *
     * @param SalesOrder $salesOrder
     * @param string $template
     * @return string
     */
    protected function getSalesOrderHtml(SalesOrder $salesOrder, string $template): string
    {
        $salesOrder->load(['client', 'user', 'status', 'quote']);
        
        $templatePath = resource_path("views/pdf/sales_orders/{$template}.php");
        
        if (!file_exists($templatePath)) {
            $templatePath = resource_path('views/pdf/sales_orders/default.php');
        }
        
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }

    /**
     * Save PDF to storage
     *
     * @param string $content
     * @param string $filename
     * @param string $disk
     * @return string Path to saved file
     */
    public function savePdf(string $content, string $filename, string $disk = 'local'): string
    {
        $path = "pdfs/{$filename}";
        \Storage::disk($disk)->put($path, $content);
        return $path;
    }
}
