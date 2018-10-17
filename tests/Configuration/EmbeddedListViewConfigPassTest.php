<?php

namespace AlterPHP\EasyAdminExtensionBundle\Tests\Configuration;

use AlterPHP\EasyAdminExtensionBundle\Configuration\EmbeddedListViewConfigPass;

class EmbeddedListViewConfigPassTest extends \PHPUnit_Framework_TestCase
{
    public function testOpenNewTabOption()
    {
        $embeddedListViewConfigPass = new EmbeddedListViewConfigPass(true);

        $backendConfig = [
            'entities' => [
                'NotSetEntity' => [
                ],
                'SetTrueEntity' => [
                    'embeddedList' => ['open_new_tab' => true],
                ],
                'SetFalseEntity' => [
                    'embeddedList' => ['open_new_tab' => false],
                ],
            ],
            'documents' => [
                'NotSetDocument' => [
                ],
                'SetTrueDocument' => [
                    'embeddedList' => ['open_new_tab' => true],
                ],
                'SetFalseDocument' => [
                    'embeddedList' => ['open_new_tab' => false],
                ],
            ],
        ];

        $backendConfig = $embeddedListViewConfigPass->process($backendConfig);

        $expectedBackendConfig = [
            'entities' => [
                'NotSetEntity' => [
                    'embeddedList' => [
                        'open_new_tab' => true,
                    ],
                ],
                'SetTrueEntity' => [
                    'embeddedList' => [
                        'open_new_tab' => true,
                    ],
                ],
                'SetFalseEntity' => [
                    'embeddedList' => [
                        'open_new_tab' => false,
                    ],
                ],
            ],
            'documents' => [
                'NotSetDocument' => [
                    'embeddedList' => [
                        'open_new_tab' => true,
                    ],
                ],
                'SetTrueDocument' => [
                    'embeddedList' => [
                        'open_new_tab' => true,
                    ],
                ],
                'SetFalseDocument' => [
                    'embeddedList' => [
                        'open_new_tab' => false,
                    ],
                ],
            ],
        ];

        $this->assertSame($backendConfig, $expectedBackendConfig);
    }
}
