<?php
namespace JuliaShpakouskaya\Learning\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;

class CartPlugin
{
    protected $_request;
    protected $_objectManager;
    protected $_productRepository;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
       $this->_request = $request;
       $this->_objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
       $this->_productRepository  = $this->_objectManager->get('\Magento\Catalog\Model\ProductRepository');

    }

    public function aroundExecute(\Magento\Checkout\Controller\Cart\Add $subject, \Closure $proceed)
    {
        $post = (array)$this->_request->getPost();
        if (!empty($post)) {
            try {
                $product = $this->_productRepository->get($post['sku']);
                $productId = $product->getId();

                $this->_request->setPostValue('product', $productId);
            }
            catch (NoSuchEntityException $e) {

            }
        }

        // call the core observed function
        $returnValue = $proceed();

        return $returnValue;
    }
}