<?php
namespace Webdev\Helloworld\Setup;
 
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class UpgradeSchema implements UpgradeSchemaInterface
{
        public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
                $installer = $setup;
 
                $installer->startSetup();
 
                if(version_compare($context->getVersion(), '1.1.0', '<')) 
                { 
			if (!$installer->tableExists('students')) {
                                $table = $installer->getConnection()->newTable(
                                        $installer->getTable('students')
                                )
                                        ->addColumn(
                                                'student_id',
                                                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                                null,
                                                [
                                                        'identity' => true,
                                                        'nullable' => false,
                                                        'primary'  => true,
                                                        'unsigned' => true,
                                                ],
                                                'Student ID'
                                        )
                                        ->addColumn(
                                                'student_name',
                                                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                                255,
                                                ['nullable => false'],
                                                'Student Name'
                                        )
                                        ->addColumn(
                                                'student_roll_no',
                                                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                                '64k',
                                                [],
                                                'Student Roll Number'
                                        )
                                        ->addColumn(
                                                'student_status',
                                                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                                1,
                                                [],
                                                'Student Status'
                                        )
                                        ->addColumn(
                                                'created_at',
                                                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                                                null,
                                                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                                                'Created At'
                                        )
                                        ->addColumn(
                                                'updated_at',
                                                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                                                null,
                                                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                                                'Updated At')
                                        ->setComment('Stundent Table');
                                $installer->getConnection()->createTable($table);
 
                                $installer->getConnection()->addIndex(
                                        $installer->getTable('students'),
                                        $setup->getIdxName(
                                                $installer->getTable('students'),
                                                ['student_name','student_roll_no'],
                                                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                                        ),
                                        ['student_name','student_roll_no'],
                                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                                );
                        }
                }
 
                $installer->endSetup();
        }
}
