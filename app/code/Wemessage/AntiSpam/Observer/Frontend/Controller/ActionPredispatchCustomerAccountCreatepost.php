<?php
/**
 * A Magento 2 module named Wemessage/AntiSpam
 * Copyright (C) 2018
 *
 * This file is part of Wemessage/AntiSpam.
 *
 * Wemessage/AntiSpam is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Wemessage\AntiSpam\Observer\Frontend\Controller;

class ActionPredispatchCustomerAccountCreatepost implements \Magento\Framework\Event\ObserverInterface
{
	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * AdminFailed constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {

    	$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    	$redirectTo = $this->scopeConfig->getValue('spam/options/redirect_page', $storeScope);

        $post = $observer->getRequest()->getPost();

        if($this->scopeConfig->getValue('spam/options/stop_cyrillic', $storeScope)){
			foreach($post as $postValue){
			    if(is_array($postValue)){
			        foreach($postValue as $val){
			            if($this->isRussian($val)){
					        header('Location: ' . $redirectTo);
					        exit;
				        }
			        }
			    } else {
			        if($this->isRussian($postValue)){
					    header('Location: ' . $redirectTo);
					    exit;
				    }
			    }

			}
		}
		if($this->scopeConfig->getValue('spam/options/stop_han', $storeScope)){
			foreach($post as $postValue){
			    if(is_array($postValue)){
			        foreach($postValue as $val){
			            if($this->isChinese($val)){
					        header('Location: ' . $redirectTo);
					        exit;
				        }
			        }
			    } else {
			        if($this->isChinese($postValue)){
					    header('Location: ' . $redirectTo);
					    exit;
				    }
			    }

			}
		}
		if($this->scopeConfig->getValue('spam/options/stop_arabic', $storeScope)){
			foreach($post as $postValue){
			    if(is_array($postValue)){
			        foreach($postValue as $val){
			            if($this->isArabian($val)){
					        header('Location: ' . $redirectTo);
					        exit;
				        }
			        }
			    } else {
			        if($this->isArabian($postValue)){
					    header('Location: ' . $redirectTo);
					    exit;
				    }
			    }

			}
		}
		if($this->scopeConfig->getValue('spam/options/limit_firstname', $storeScope) <= strlen($post['firstname'])){
			header('Location: ' . $redirectTo);
			exit;
		}
		if($this->scopeConfig->getValue('spam/options/limit_lastname', $storeScope) <= strlen($post['lastname'])){
			header('Location: ' . $redirectTo);
			exit;
		}
    }

    private function isRussian($text) {
		return (bool) preg_match('/[\p{Cyrillic}]/u', $this->convertToUtf8($text));
	}
	private function isChinese($text) {
        return (bool) preg_match('/[\p{Han}]/u', $this->convertToUtf8($text));
    }

    private function isArabian($text) {
        return (bool) preg_match('/[\p{Arabic}]/u', $this->convertToUtf8($text));
    }
	private function convertToUtf8($text){
	    return ((bool)iconv(mb_detect_encoding($text), "UTF-8", $text) ? iconv(mb_detect_encoding($text), "UTF-8", $text) : utf8_encode($text));
    }

}
