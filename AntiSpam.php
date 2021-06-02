<?php
/*************************************************************************************/
/*      This file is part of the module AntiSpam                                     */
/*                                                                                   */
/*      Copyright (c) Pixel Plurimedia                                               */
/*      email : dev@pixel-plurimedia.fr                                              */
/*      web : https://pixel-plurimedia.fr                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace AntiSpam;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Model\Base\ModuleConfigQuery;
use Thelia\Module\BaseModule;
use Thelia\Core\Template\TemplateDefinition;

/**
 * Class AntiSpam
 * @package AntiSpam
 * @author Charles Anguenot <charles.anguenot@gmail.com>, Thierry Caresmel <thierry@pixel-plurimedia.fr>
 */
class AntiSpam extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'antispam';
    /** Forms */
    const CONTACT = 'thelia_contact';
    const CUSTOMER_CREATE = 'thelia_customer_create';
    const NEWSLETTER = 'thelia_newsletter';
    const NEWSLETTER_UNSUBSCRIBE = 'thelia_form_newsletterunsubscribeform';

    public function postActivation(ConnectionInterface $con = null)
    {
        $antispamConfig = [
            'honeypot' => 1,
            'form_fill_duration' => 1,
            'form_fill_duration_limit' => 3,
            'question' => 1
        ];

        self::setConfigValue('antispam_config', json_encode($antispamConfig));
    }

    public function destroy(ConnectionInterface $con = null, $deleteModuleData = false)
    {
        if ($deleteModuleData) {
            $configQuery = ModuleConfigQuery::create();
            $configQuery->deleteConfigValue(self::getModuleId(), 'antispam_config');
        }
    }

    public function getHooks()
    {
        return [
            [
                "type" => TemplateDefinition::FRONT_OFFICE,
                "code" => "pxlpluri.antispam",
                "title" => "Hook PxlPluriAntispam",
                "active" => true
            ],
        ];
    }
}
