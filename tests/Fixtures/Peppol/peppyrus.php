<?php

/**
 * Test fixtures for Peppyrus Peppol provider
 * 
 * Centralized test data for Peppyrus operations
 */

return [
    'transmission' => [
        'basic' => [
            'request' => [
                'document' => '<Invoice/>',
                'recipient' => '0088:1234567890123',
            ],
            'response' => [
                'transmission_id' => 'trans-123',
                'status' => 'transmitted',
            ],
        ],
        'with_metadata' => [
            'request' => [
                'document' => '<Invoice/>',
                'recipient' => '0088:9999999999999',
                'metadata' => ['priority' => 'high'],
            ],
            'response' => [
                'transmission_id' => 'trans-meta',
            ],
        ],
    ],
    
    'transmission_status' => [
        'delivered' => [
            'transmission_id' => 'trans-456',
            'response' => [
                'transmission_id' => 'trans-456',
                'status' => 'delivered',
                'updated_at' => '2024-01-15T10:30:00Z',
            ],
        ],
        'pending' => [
            'transmission_id' => 'trans-pending',
            'response' => [
                'transmission_id' => 'trans-pending',
                'status' => 'pending',
            ],
        ],
        'failed' => [
            'transmission_id' => 'trans-failed',
            'response' => [
                'transmission_id' => 'trans-failed',
                'status' => 'failed',
                'error' => 'Network timeout',
            ],
        ],
    ],
    
    'transmission_retry' => [
        'transmission_id' => 'trans-retry',
        'response' => [
            'transmission_id' => 'trans-retry',
            'status' => 'retrying',
            'retry_attempt' => 1,
        ],
    ],
    
    'acknowledgment' => [
        'acknowledged' => [
            'transmission_id' => 'trans-123',
            'response' => [
                'transmission_id' => 'trans-123',
                'acknowledged' => true,
                'acknowledged_at' => '2024-01-15T11:00:00Z',
            ],
        ],
        'pending' => [
            'transmission_id' => 'trans-pending',
            'response' => [
                'transmission_id' => 'trans-pending',
                'acknowledged' => false,
                'status' => 'pending',
            ],
        ],
    ],
    
    'acknowledgment_details' => [
        'received' => [
            'acknowledgment_id' => 'ack-456',
            'response' => [
                'acknowledgment_id' => 'ack-456',
                'status' => 'received',
                'type' => 'business_acknowledgment',
                'details' => ['message' => 'Invoice accepted'],
            ],
        ],
        'rejected' => [
            'acknowledgment_id' => 'ack-negative',
            'response' => [
                'acknowledgment_id' => 'ack-negative',
                'status' => 'rejected',
                'type' => 'business_acknowledgment',
                'reason' => 'Invalid invoice data',
            ],
        ],
    ],
    
    'access_point_query' => [
        'found' => [
            'participant_id' => '0088:1234567890123',
            'document_type' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            'response' => [
                'participant_id' => '0088:1234567890123',
                'access_point_url' => 'https://ap.example.com',
                'certificate' => 'cert-data',
            ],
        ],
        'credit_note' => [
            'participant_id' => '0088:9876543210987',
            'document_type' => 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2',
            'response' => [
                'participant_id' => '0088:9876543210987',
                'access_point_url' => 'https://ap.creditnote.com',
                'supports_document_type' => true,
            ],
        ],
        'not_found' => [
            'participant_id' => '0088:0000000000000',
            'document_type' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            'response' => [
                'participant_id' => '0088:0000000000000',
                'found' => false,
                'error' => 'No access point registered for participant',
            ],
        ],
    ],
    
    'access_point_metadata' => [
        'access_point_id' => 'ap-456',
        'response' => [
            'access_point_id' => 'ap-456',
            'url' => 'https://ap.test.com',
            'provider' => 'Test Provider',
            'capabilities' => ['invoice', 'credit-note'],
        ],
    ],
    
    'compliance_validation' => [
        'compliant' => [
            'request' => ['document' => '<Invoice/>'],
            'response' => [
                'compliant' => true,
                'errors' => [],
                'warnings' => [],
            ],
        ],
        'non_compliant' => [
            'request' => ['document' => '<InvalidInvoice/>'],
            'response' => [
                'compliant' => false,
                'errors' => ['Missing required element: cbc:ID'],
                'warnings' => ['Recommended element missing: cbc:Note'],
            ],
        ],
    ],
    
    'validation_report' => [
        'passed' => [
            'validation_id' => 'val-123',
            'response' => [
                'validation_id' => 'val-123',
                'compliant' => true,
                'checked_at' => '2024-01-15T10:30:00Z',
                'rules_checked' => 150,
                'rules_passed' => 150,
            ],
        ],
        'failed' => [
            'validation_id' => 'val-detailed',
            'response' => [
                'validation_id' => 'val-detailed',
                'compliant' => false,
                'rules_checked' => 150,
                'rules_passed' => 147,
                'rules_failed' => 3,
                'failed_rules' => [
                    ['rule' => 'BR-01', 'message' => 'Invoice must have an ID'],
                    ['rule' => 'BR-02', 'message' => 'Invoice must have an issue date'],
                ],
            ],
        ],
    ],
];
