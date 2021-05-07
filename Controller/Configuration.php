<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace AntiSpam\Controller;

use AntiSpam\AntiSpam;
use AntiSpam\Model\Config\AntiSpamConfigValue;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

class Configuration extends BaseAdminController
{
    public function editConfiguration()
    {
        if (null !== $response = $this->checkAuth(
            AdminResources::MODULE,
            [AntiSpam::DOMAIN_NAME],
            AccessManager::UPDATE
        )) {
            return $response;
        }

        $form = $this->createForm('antispam.configuration');
        $error_message = null;

        try {
            $validateForm = $this->validateForm($form);
            $data = $validateForm->getData();

            AntiSpam::setConfigValue('honeypot', is_bool($data["honeypot"]) ? (int) ($data["honeypot"]) : $data["honeypot"]);
            AntiSpam::setConfigValue('form_fill_duration', is_bool($data["form_fill_duration"]) ? (int) ($data["form_fill_duration"]) : $data["form_fill_duration"]);
            AntiSpam::setConfigValue('question', is_bool($data["question"]) ? (int) ($data["question"]) : $data["question"]);
            AntiSpam::setConfigValue('calculation', is_bool($data["calculation"]) ? (int) ($data["calculation"]) : $data["calculation"]);

            return $this->redirectToConfigurationPage();

        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        }

        if (null !== $error_message) {
            $this->setupFormErrorContext(
                'configuration',
                $error_message,
                $form
            );
            $response = $this->render("module-configure", ['module_code' => 'AntiSpam']);
        }
        return $response;
    }

    /**
     * Redirect to the configuration page
     */
    protected function redirectToConfigurationPage()
    {
        return RedirectResponse::create(URL::getInstance()->absoluteUrl('/admin/module/AntiSpam'));
    }
}
