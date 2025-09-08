<?php
namespace Josequal\APIMobile\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade data for Custom Points System
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0', '==')) {
            $this->addSampleData($setup);
        }

        $setup->endSetup();
    }

    /**
     * Add sample data for testing
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function addSampleData(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();

        // Add sample points data
        $samplePoints = [
            [
                'customer_id' => 1,
                'points' => 100,
                'action' => 'signup',
                'description' => 'Welcome bonus for new customer',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'customer_id' => 1,
                'points' => 50,
                'action' => 'review',
                'description' => 'Product review bonus',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'customer_id' => 1,
                'points' => 200,
                'action' => 'order',
                'description' => 'Order completion bonus',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'customer_id' => 2,
                'points' => 150,
                'action' => 'signup',
                'description' => 'Welcome bonus for new customer',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'customer_id' => 2,
                'points' => 75,
                'action' => 'order',
                'description' => 'Order completion bonus',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($samplePoints as $point) {
            $connection->insert($setup->getTable('custom_points'), $point);
        }

        // Add sample balance data
        $sampleBalance = [
            [
                'customer_id' => 1,
                'total_points' => 350,
                'used_points' => 0,
                'available_points' => 350,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'customer_id' => 2,
                'total_points' => 225,
                'used_points' => 0,
                'available_points' => 225,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($sampleBalance as $balance) {
            $connection->insertOnDuplicate(
                $setup->getTable('custom_points_balance'),
                $balance,
                ['total_points', 'used_points', 'available_points', 'updated_at']
            );
        }
    }
}
