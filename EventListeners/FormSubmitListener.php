<?php

namespace AntiSpam\EventListeners;

use AntiSpam\AntiSpam;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Thelia\Core\Event\Contact\ContactEvent;
use Thelia\Core\Event\Customer\CustomerCreateOrUpdateEvent;
use Thelia\Core\Event\Newsletter\NewsletterEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;

/**
 * [Description FormAfterBuildListener]
 */
class FormSubmitListener implements EventSubscriberInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param ContactEvent $event
     */
    public function checkContactEvent(ContactEvent $event)
    {
        $this->checkSubmit($event->getForm()->getData());
    }

    /**
     * @param CustomerCreateOrUpdateEvent $event
     */
    public function checkCustomerCreation(CustomerCreateOrUpdateEvent $event)
    {
        if (null !== $customerCreateForm = $this->request->request->get(Antispam::CUSTOMER_CREATE)) $this->checkSubmit($customerCreateForm);
    }

    /**
     * @param NewsletterEvent $event
     */
    public function checkNewsletterEvent(NewsletterEvent $event)
    {
        if (null !== $subscribeForm = $this->request->request->get(Antispam::NEWSLETTER)) $this->checkSubmit($subscribeForm);
        else if (null !== $unsubscribeForm = $this->request->request->get(Antispam::NEWSLETTER_UNSUBSCRIBE)) $this->checkSubmit($unsubscribeForm);
    }

    /**
     * @param ContactEvent $event
     */
    protected function checkSubmit($data)
    {
        $isSpam = false;
        $config = json_decode(AntiSpam::getConfigValue('antispam_config'), true);

        //honeypot
        if ($config['honeypot'] && null != $data['bear']) $isSpam = true;
        //question
        if ($config['question'] && $this->cleanString($this->request->getSession()->get('questionAnswer')) !== $this->cleanString($data['questionAnswer'])) {
            $isSpam = true;
        }

        // form filling duration
        if ($config['form_fill_duration']) {
            $dateFormSubmission = new \DateTimeImmutable($data['form_load_time']);
            if ($config['form_fill_duration_limit'] > $dateFormSubmission->diff(new \DateTimeImmutable())->format('%s')) {
                $isSpam = true;
            }
        }

        if ($isSpam) {
            // Throw exception if spam detected
            throw new FormValidationException(
                Translator::getInstance()->trans("An error occured during the Antispam verification. Please try again", [], Antispam::DOMAIN_NAME)
            );
        }
    }

    protected function cleanString($string)
    {
        return strtr(
            utf8_decode(
                mb_strtolower(str_replace('-', ' ', $string))
            ),
            utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
        );
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::CONTACT_SUBMIT => ['checkContactEvent', 512],
            TheliaEvents::CUSTOMER_CREATEACCOUNT => ['checkCustomerCreation', 512],
            TheliaEvents::NEWSLETTER_SUBSCRIBE => ['checkNewsletterEvent', 512],
            TheliaEvents::NEWSLETTER_UNSUBSCRIBE => ['checkNewsletterEvent', 512],
        ];
    }
}
