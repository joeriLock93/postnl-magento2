<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Grid\Order;

use TIG\PostNL\Block\Adminhtml\Grid\Order\ProductCode;
use TIG\PostNL\Block\Adminhtml\Renderer\ProductCode as Renderer;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Test\TestCase;

class ProductCodeTest extends TestCase
{
    public $instanceClass = ProductCode::class;

    public function getInstance(array $args = [])
    {
        if (!isset($args['context'])) {
            $contextMock = $this->getMockForAbstractClass(ContextInterface::class, [], '', false, true, true, []);
            $processor   = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\Processor')
                ->disableOriginalConstructor()
                ->getMock();
            $contextMock->expects($this->any())->method('getProcessor')->willReturn($processor);

            $args['context'] = $contextMock;
        }

        return parent::getInstance($args);
    }

    public function getDataProvider()
    {
        return [
            'pakjegemak label' => ['3543', 'Pakjegemak (3543)'],
            'standard label'   => ['3085', 'Standard (3085)']
        ];
    }

    /**
     * @param $productCode
     * @param $expects
     *
     * @dataProvider getDataProvider
     */
    public function testGetCellContents($productCode, $expects)
    {
        $orderId = rand(1,4);

        $repoMock = $this->getFakeMock(OrderRepositoryInterface::class)->getMock();
        $repoExpects = $repoMock->expects($this->once());
        $repoExpects->method('getByOrderId');
        $repoExpects->with($orderId);
        $repoExpects->willReturn($this->getOrder($productCode));

        $rendererMock = $this->getFakeMock(Renderer::class)->getMock();
        $rendererExpects = $rendererMock->expects($this->once());
        $rendererExpects->method('render');
        $rendererExpects->with($productCode);
        $rendererExpects->willReturn($expects);

        $instance = $this->getInstance([
            'orderRepository' => $repoMock,
            'codeRenderer' => $rendererMock
        ]);

        $result = $this->invokeArgs('getCellContents', [['entity_id' => $orderId]], $instance);

        $this->assertEquals($expects, $result);
    }

    private function getOrder($productCode)
    {
        $orderMock = $this->getFakeMock(\TIG\PostNL\Model\Order::class)->getMock();

        $setDataExpects = $orderMock->expects($this->atLeastOnce());
        $setDataExpects->method('getProductCode');
        $setDataExpects->willReturn($productCode);

        return $orderMock;
    }
}
