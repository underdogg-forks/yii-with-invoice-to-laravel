<?php

/**
 * Test fixtures for E-invoicing.be Peppol provider
 * 
 * Centralized test data for Belgian Peppol operations
 */

return [
    'invoice_submission' => [
        'request' => [
            'document' => '<Invoice/>',
            'vat_number' => 'BE0123456789',
        ],
        'response' => [
            'submission_id' => 'sub-be-123',
            'status' => 'accepted',
        ],
        'with_belgian_vat' => [
            'request' => [
                'document' => '<Invoice/>',
                'vat_number' => 'BE0987654321',
                'belgian_specific_data' => ['structured_communication' => '+++123/4567/89012+++'],
            ],
            'response' => [
                'submission_id' => 'sub-be-vat',
            ],
        ],
    ],
    
    'submission_status' => [
        'processed' => [
            'submission_id' => 'sub-be-456',
            'response' => [
                'submission_id' => 'sub-be-456',
                'status' => 'processed',
                'processed_at' => '2024-01-15T10:30:00Z',
            ],
        ],
        'pending' => [
            'submission_id' => 'sub-be-pending',
            'response' => [
                'submission_id' => 'sub-be-pending',
                'status' => 'pending',
            ],
        ],
        'rejected' => [
            'submission_id' => 'sub-be-rejected',
            'response' => [
                'submission_id' => 'sub-be-rejected',
                'status' => 'rejected',
                'reason' => 'Invalid VAT number format',
            ],
        ],
    ],
    
    'submission_cancel' => [
        'submission_id' => 'sub-be-789',
        'response' => [
            'submission_id' => 'sub-be-789',
            'status' => 'cancelled',
        ],
    ],
    
    'compliance_check' => [
        'request' => [
            'document' => '<Invoice/>',
            'vat_number' => 'BE0123456789',
        ],
        'response' => [
            'compliant' => true,
            'belgian_requirements_met' => true,
        ],
    ],
    
    'participant_lookup' => [
        'registered' => [
            'participant_id' => '0208:BE0123456789',
            'response' => [
                'participant_id' => '0208:BE0123456789',
                'registered' => true,
                'company_name' => 'Belgian Company BVBA',
            ],
        ],
        'unregistered' => [
            'participant_id' => '0208:BE0000000000',
            'response' => [
                'participant_id' => '0208:BE0000000000',
                'registered' => false,
                'message' => 'Participant not found in Belgian Peppol network',
            ],
        ],
        'belgian_endpoint' => [
            'vat_number' => 'BE0987654321',
            'response' => [
                'vat_number' => 'BE0987654321',
                'endpoint_url' => 'https://ap.einvoicing.be',
                'supports_peppol' => true,
            ],
        ],
        'belgian_endpoint_with_capabilities' => [
            'vat_number' => 'BE0555555555',
            'response' => [
                'vat_number' => 'BE0555555555',
                'endpoint_url' => 'https://ap.test.be',
                'supports_peppol' => true,
                'capabilities' => ['invoice', 'credit-note', 'application-response'],
            ],
        ],
        'endpoint_not_found' => [
            'vat_number' => 'BE0111111111',
            'response' => [
                'vat_number' => 'BE0111111111',
                'supports_peppol' => false,
                'error' => 'No Peppol endpoint registered for this VAT number',
            ],
        ],
    ],
    
    'vat_validation' => [
        'valid' => [
            'vat_number' => 'BE0123456789',
            'response' => [
                'vat_number' => 'BE0123456789',
                'valid' => true,
                'company_name' => 'Test Company BVBA',
                'address' => 'Brussels, Belgium',
            ],
        ],
        'invalid' => [
            'vat_number' => 'BE9999999999',
            'response' => [
                'vat_number' => 'BE9999999999',
                'valid' => false,
                'message' => 'Invalid Belgian VAT number',
            ],
        ],
    ],
    
    'status_tracking' => [
        'processing' => [
            'submission_id' => 'sub-be-123',
            'response' => [
                'submission_id' => 'sub-be-123',
                'status' => 'processing',
                'last_updated' => '2024-01-01T12:00:00Z',
            ],
        ],
        'delivered' => [
            'submission_id' => 'sub-be-456',
            'response' => [
                'submission_id' => 'sub-be-456',
                'status' => 'delivered',
                'delivered_at' => '2024-01-01T12:30:00Z',
            ],
        ],
    ],
];
