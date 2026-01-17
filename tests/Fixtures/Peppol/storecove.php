<?php

/**
 * Test fixtures for StoreCove Peppol provider
 * 
 * Centralized test data for StoreCove operations
 */

return [
    'document_submission' => [
        'basic' => [
            'request' => [
                'legal_entity_id' => 123,
                'document' => '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"/>',
            ],
            'response' => [
                'guid' => 'doc-guid-123',
                'status' => 'submitted',
            ],
        ],
        'with_routing' => [
            'request' => [
                'legal_entity_id' => 456,
                'document' => '<Invoice/>',
                'routing' => [
                    'eIdentifiers' => [
                        ['scheme' => '0088', 'id' => '1234567890123']
                    ]
                ],
            ],
            'response' => [
                'guid' => 'doc-routed-123',
            ],
        ],
        'with_attachments' => [
            'request' => [
                'legal_entity_id' => 789,
                'document' => '<Invoice/>',
                'attachments' => [
                    ['filename' => 'attachment.pdf', 'content' => 'base64content']
                ],
            ],
            'response' => [
                'guid' => 'doc-attach-456',
            ],
        ],
    ],
    
    'document_status' => [
        'delivered' => [
            'document_id' => 'doc-guid-456',
            'response' => [
                'guid' => 'doc-guid-456',
                'status' => 'delivered',
                'updated_at' => '2024-01-15T10:30:00Z',
            ],
        ],
        'cancelled' => [
            'document_id' => 'doc-guid-cancel',
            'response' => [
                'guid' => 'doc-guid-cancel',
                'status' => 'cancelled',
            ],
        ],
    ],
    
    'document_retrieval' => [
        'full_document' => [
            'document_id' => 'doc-guid-789',
            'response' => [
                'guid' => 'doc-guid-789',
                'document' => '<Invoice>...</Invoice>',
                'metadata' => ['sender' => 'Company A'],
            ],
        ],
    ],
    
    'delivery_status' => [
        'delivered' => [
            'document_id' => 'doc-123',
            'response' => [
                'status' => 'delivered',
                'delivered_at' => '2024-01-15T10:30:00Z',
            ],
        ],
        'pending' => [
            'document_id' => 'doc-pending',
            'response' => [
                'status' => 'pending',
                'last_updated' => '2024-01-15T09:15:00Z',
            ],
        ],
        'failed' => [
            'document_id' => 'doc-failed',
            'response' => [
                'status' => 'failed',
                'error' => 'Recipient not found',
                'failed_at' => '2024-01-15T10:00:00Z',
            ],
        ],
    ],
    
    'delivery_history' => [
        'document_id' => 'doc-456',
        'response' => [
            [
                'event' => 'submitted',
                'timestamp' => '2024-01-15T09:00:00Z',
            ],
            [
                'event' => 'delivered',
                'timestamp' => '2024-01-15T10:30:00Z',
            ],
        ],
    ],
    
    'recipient_acknowledgment' => [
        'acknowledged' => [
            'document_id' => 'doc-789',
            'response' => [
                'acknowledged' => true,
                'acknowledged_at' => '2024-01-15T11:00:00Z',
                'acknowledgment_type' => 'read',
            ],
        ],
    ],
    
    'validation' => [
        'valid_document' => [
            'request' => ['document' => '<Invoice/>'],
            'response' => [
                'valid' => true,
                'errors' => [],
                'warnings' => [],
            ],
        ],
        'invalid_document' => [
            'request' => ['document' => '<InvalidInvoice/>'],
            'response' => [
                'valid' => false,
                'errors' => ['Missing required element: cbc:ID'],
                'warnings' => ['Recommended element missing: cbc:Note'],
            ],
        ],
        'valid_participant' => [
            'participant_id' => '0088:1234567890123',
            'document_type' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            'response' => [
                'valid' => true,
                'participant_id' => '0088:1234567890123',
            ],
        ],
        'invalid_participant' => [
            'participant_id' => '9999:0000000000000',
            'document_type' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            'response' => [
                'valid' => false,
                'error' => 'Participant not found in network',
            ],
        ],
        'valid_syntax' => [
            'xml' => '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"></Invoice>',
            'response' => [
                'valid' => true,
                'syntax_errors' => [],
            ],
        ],
    ],
    
    'webhooks' => [
        'create' => [
            'request' => [
                'url' => 'https://example.com/webhook',
                'events' => ['document.submitted', 'document.delivered'],
            ],
            'response' => [
                'id' => 'webhook-123',
                'url' => 'https://example.com/webhook',
            ],
        ],
        'create_with_secret' => [
            'request' => [
                'url' => 'https://example.com/webhook',
                'events' => ['document.submitted'],
                'secret' => 'webhook-secret-key',
            ],
            'response' => [
                'id' => 'webhook-secure',
                'url' => 'https://example.com/webhook',
            ],
        ],
        'get' => [
            'webhook_id' => 'webhook-456',
            'response' => [
                'id' => 'webhook-456',
                'url' => 'https://example.com/webhook',
                'events' => ['document.delivered'],
            ],
        ],
        'delete' => [
            'webhook_id' => 'webhook-789',
            'response' => ['deleted' => true],
        ],
        'test' => [
            'webhook_id' => 'webhook-test',
            'response' => [
                'test_sent' => true,
                'response_code' => 200,
            ],
        ],
    ],
    
    'legal_entities' => [
        'create' => [
            'request' => [
                'party_name' => 'Test Company BV',
                'identifiers' => [
                    ['scheme' => '0088', 'id' => '1234567890123']
                ],
            ],
            'response' => [
                'id' => 123,
                'party_name' => 'Test Company BV',
            ],
        ],
        'get' => [
            'entity_id' => '456',
            'response' => [
                'id' => 456,
                'party_name' => 'Example Corp',
            ],
        ],
        'update' => [
            'entity_id' => '789',
            'request' => ['party_name' => 'Updated Company Name'],
            'response' => [
                'id' => 789,
                'party_name' => 'Updated Company Name',
            ],
        ],
        'list_without_filters' => [
            'response' => [
                ['id' => 1, 'party_name' => 'Company 1'],
                ['id' => 2, 'party_name' => 'Company 2'],
            ],
        ],
        'list_with_filters' => [
            'filters' => ['page' => 2, 'per_page' => 10],
            'response' => [
                ['id' => 11, 'party_name' => 'Company 11'],
            ],
        ],
    ],
];
