<?php
/**
 * @author Bloomreach
 * @copyright Copyright (c) Bloomreach (https://www.bloomreach.com/)
 */
declare(strict_types=1);

namespace Bloomreach\EngagementConnector\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Fronted model for rendering the preconfigurate initial import buttons
 */
class PreconfigurateImportButton extends Button
{
    /**
     * Checks whether is component enabled
     *
     * @return bool
     */
    protected function isEnabled(): bool
    {
        return true;
    }
}
