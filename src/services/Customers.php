<?php
/**
 * Stripe Payments plugin for Craft CMS 3.x
 *
 * @link      https://enupal.com/
 * @copyright Copyright (c) 2018 Enupal LLC
 */

namespace enupal\stripe\services;

use Craft;
use enupal\stripe\Stripe as StripePlugin;
use Stripe\Customer;
use Stripe\Invoice;
use yii\base\Component;
use enupal\stripe\records\Customer as CustomerRecord;


class Customers extends Component
{
    /**
     * @param $email
     * @param $stripeId
     * @param bool $testMode
     * @return CustomerRecord
     */
    public function createCustomer($email, $stripeId, $testMode = true)
    {
        $customerRecord = new CustomerRecord();
        $customerRecord->email = $email;
        $customerRecord->stripeId = $stripeId;
        $customerRecord->testMode = $testMode;
        $customerRecord->save(false);

        return $customerRecord;
    }

    /**
     * @param $id
     * @return Customer|null
     * @throws \Exception
     */
    public function getStripeCustomer($id)
    {
        StripePlugin::$app->settings->initializeStripe();
        $stripeCustomer = null;
        try{
            $stripeCustomer = Customer::retrieve($id);
        }
        catch (\Exception $e){
            Craft::error($e->getMessage(). " - getting a new customer");
        }

        return $stripeCustomer;
    }

    /**
     * @param $id
     * @return Invoice|null
     * @throws \Exception
     */
    public function getStripeInvoice($id)
    {
        StripePlugin::$app->settings->initializeStripe();
        $invoice = null;
        try{
            $invoice = Invoice::retrieve($id);
        }
        catch (\Exception $e){
            Craft::error($e->getMessage(). " - getting an invoice");
        }

        return $invoice;
    }

    /**
     * @param Customer $customer
     * @param $testMode
     */
    public function registerCustomer(Customer $customer, $testMode)
    {
        $customerRecord = CustomerRecord::findOne([
            'email' => $customer['email'],
            'testMode' => $testMode
        ]);

        if ($customerRecord === null){
            StripePlugin::$app->customers->createCustomer($customer['email'], $customer['id'], $testMode);
        }
    }
}
