<?php
namespace JuliaShpakouskaya\Learning\Controller\Index;

use Magento\Framework\Exception\NoSuchEntityException;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_objectManager;
    protected $_productRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        $this->_objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $this->_productRepository  = $this->_objectManager->get('\Magento\Catalog\Model\ProductRepository');

        return parent::__construct($context);
    }

    public function execute()
    {
        $post = (array)$this->getRequest()->getPost();
        if (!empty($post)) {
            try {
                $product = $this->_productRepository->get($post['sku']);
                $productId = $product->getId();
                $cart = $this->_objectManager->create('Magento\Checkout\Model\Cart');
                $formKey = $post['form_key'];
                $params = [
                    'form_key' => $formKey,
                    'product' => $productId, //product Id
                    'qty'   =>1, //quantity of product
                ];
                $cart->addProduct($product, $params);
                $cart->save();
                $this->messageManager->addSuccessMessage(__('Item added to the cart successfully.'));
            }
            catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('Item with SKU ' . $post['sku']. ' does not exist!' ));
            }
        }
        return $this->_pageFactory->create();
    }
}
