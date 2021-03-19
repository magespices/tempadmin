<?php
/**
 * Created by Q-Solutions Studio
 *
 * @category    Magespices
 * @package     Magespices_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

declare(strict_types=1);

namespace Magespices\TempAdmin\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 */
class Index implements HttpGetActionInterface
{

    /** @var PageFactory */
    private PageFactory $pageFactory;

    /** @var RequestInterface */
    private RequestInterface $request;

    /**
     * Create constructor.
     * @param PageFactory $pageFactory
     * @param RequestInterface $request
     */
    public function __construct(PageFactory $pageFactory, RequestInterface $request)
    {
        $this->pageFactory = $pageFactory;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        return $this->pageFactory->create();
    }
}
