<?php

/**
 * Test fixtures for LetsPeppol provider
 * 
 * Centralized test data for LetsPeppol operations
 */

return [
    'invoice_submission' => [
        'basic' => [
            'request' => [
                'document' => '<Invoice/>',
                'recipient' => '0088:1234567890123',
            ],
            'response' => [
                'invoice_id' => 'inv-123',
                'status' => 'sent',
            ],
        ],
        'with_metadata' => [
            'request' => [
                'document' => '<Invoice/>',
                'recipient' => '0088:9876543210987',
                'metadata' => ['reference' => 'PO-12345'],
            ],
            'response' => [
                'invoice_id' => 'inv-meta',
                'status' => 'sent',
            ],
        ],
    ],
    
    'invoice_status' => [
        'delivered' => [
            'invoice_id' => 'inv-456',
            'response' => [
                'invoice_id' => 'inv-456',
                'status' => 'delivered',
                'updated_at' => '2024-01-15T10:30:00Z',
            ],
        ],
        'pending' => [
            'invoice_id' => 'inv-pending',
            'response' => [
                'invoice_id' => 'inv-pending',
                'status' => 'pending',
            ],
        ],
        'cancelled' => [
            'invoice_id' => 'inv-789',
            'response' => [
                'invoice_id' => 'inv-789',
                'status' => 'cancelled',
            ],
        ],
    ],
    
    'delivery_status' => [
        'delivered' => [
            'invoice_id' => 'inv-123',
            'response' => [
                'invoice_id' => 'inv-123',
                'status' => 'delivered',
                'delivered_at' => '2024-01-15T10:30:00Z',
            ],
        ],
        'pending' => [
            'invoice_id' => 'inv-pending',
            'response' => [
                'invoice_id' => 'inv-pending',
                'status' => 'pending',
            ],
        ],
        'failed' => [
            'invoice_id' => 'inv-failed',
            'response' => [
                'invoice_id' => 'inv-failed',
                'status' => 'failed',
                'error' => 'Recipient endpoint unavailable',
            ],
        ],
    ],
    
    'delivery_report' => [
        'invoice_id' => 'inv-456',
        'response' => [
            'invoice_id' => 'inv-456',
            'events' => [
                ['event' => 'sent', 'timestamp' => '2024-01-15T09:00:00Z'],
                ['event' => 'delivered', 'timestamp' => '2024-01-15T10:30:00Z'],
            ],
        ],
    ],
    
    'participant_lookup' => [
        'registered' => [
            'participant_id' => '0088:1234567890123',
            'response' => [
                'participant_id' => '0088:1234567890123',
                'name' => 'Test Company',
                'registered' => true,
            ],
        ],
        'unregistered' => [
            'participant_id' => '0088:0000000000000',
            'response' => [
                'participant_id' => '0088:0000000000000',
                'registered' => false,
            ],
        ],
    ],
    
    'participant_details' => [
        'participant_id' => '0088:9876543210987',
        'response' => [
            'participant_id' => '0088:9876543210987',
            'name' => 'Example Corp',
            'endpoints' => ['https://ap.example.com'],
            'capabilities' => ['invoice', 'credit-note'],
        ],
    ],
    
    'endpoint_validation' => [
        'valid_invoice' => [
            'participant_id' => '0088:5555555555555',
            'document_type' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            'response' => [
                'valid' => true,
                'endpoint' => 'https://ap.test.com',
            ],
        ],
        'valid_credit_note' => [
            'participant_id' => '0088:7777777777777',
            'document_type' => 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2',
            'response' => [
                'valid' => true,
                'endpoint' => 'https://ap.example.com',
                'supports_document_type' => true,
            ],
        ],
    ],
    
    'validation' => [
        'valid_invoice' => [
            'request' => ['document' => '<Invoice/>'],
            'response' => [
                'valid' => true,
                'errors' => [],
                'warnings' => [],
            ],
        ],
        'invalid_invoice' => [
            'request' => ['document' => '<InvalidInvoice/>'],
            'response' => [
                'valid' => false,
                'errors' => ['Missing required field: InvoiceNumber'],
                'warnings' => ['Optional field missing: PaymentMeans'],
            ],
        ],
    ],
    
    'compliance_check' => [
        'compliant_default' => [
            'xml' => '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"/>',
            'specification' => 'bis3',
            'response' => [
                'compliant' => true,
                'specification' => 'bis3',
            ],
        ],
        'compliant_custom' => [
            'xml' => '<Invoice/>',
            'specification' => 'peppol-bis-3.0',
            'response' => [
                'compliant' => true,
                'specification' => 'peppol-bis-3.0',
            ],
        ],
        'non_compliant' => [
            'xml' => '<Invoice/>',
            'response' => [
                'compliant' => false,
                'violations' => ['Invalid document structure'],
            ],
        ],
    ],
];
