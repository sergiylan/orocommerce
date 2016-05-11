<?php

namespace OroB2B\Bundle\PaymentBundle\Tests\Unit\Method;

use OroB2B\Bundle\PaymentBundle\DependencyInjection\Configuration;
use OroB2B\Bundle\PaymentBundle\Method\PayPalPaymentsPro;
use OroB2B\Bundle\PaymentBundle\PayPal\Payflow\Option;

class PayPalPaymentsProTest extends AbstractPayflowGatewayTest
{
    /** @var PayPalPaymentsPro */
    protected $method;

    protected function setUp()
    {
        parent::setUp();
        $this->method = new PayPalPaymentsPro($this->gateway, $this->configManager, $this->router);
    }

    protected function tearDown()
    {
        unset($this->method);
        parent::tearDown();
    }

    public function testIsEnabled()
    {
        $this->setConfig($this->once(), Configuration::PAYPAL_PAYMENTS_PRO_ENABLED_KEY, true);
        $this->assertTrue($this->method->isEnabled());
    }

    /**
     * {@inheritdoc}
     */
    protected function configureConfig(array $configs = [])
    {
        $configs = array_merge(
            [
                Configuration::PAYPAL_PAYMENTS_PRO_VENDOR_KEY => 'test_vendor',
                Configuration::PAYPAL_PAYMENTS_PRO_USER_KEY => 'test_user',
                Configuration::PAYPAL_PAYMENTS_PRO_PASSWORD_KEY => 'test_password',
                Configuration::PAYPAL_PAYMENTS_PRO_PARTNER_KEY => 'test_partner',
                Configuration::PAYPAL_PAYMENTS_PRO_TEST_MODE_KEY => true,
            ],
            $configs
        );

        parent::configureConfig($configs);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function executeDataProvider()
    {
        return [
            'authorize successful' => [
                [
                    'gatewayAction' => Option\Transaction::AUTHORIZATION,
                    'sourceTransactionData' => [],
                    'transactionData' => [
                        'action' => 'authorize',
                        'request' => [],
                    ],
                    'configs' => [
                        Configuration::PAYPAL_PAYMENTS_PRO_PAYMENT_ACTION_KEY => 'authorize',
                    ],
                    'requestOptions' => [
                        'VENDOR' => 'test_vendor',
                        'USER' => 'test_user',
                        'PWD' => 'test_password',
                        'PARTNER' => 'test_partner',
                    ],
                    'responseData' => [
                        'RESULT' => '0',
                        'PNREF' => 'test_reference',
                    ],
                ],
                []
            ],
            'authorize successful with amount enabled' => [
                [
                    'gatewayAction' => Option\Transaction::AUTHORIZATION,
                    'sourceTransactionData' => [
                        'reference' => 'test_reference',
                        'request' => [],
                    ],
                    'transactionData' => [
                        'action' => 'authorize',
                        'request' => [],
                    ],
                    'configs' => [
                        Configuration::PAYPAL_PAYMENTS_PRO_PAYMENT_ACTION_KEY => 'authorize',
                        Configuration::PAYFLOW_GATEWAY_AUTHORIZATION_FOR_REQUIRED_AMOUNT_KEY => true
                    ],
                    'requestOptions' => [
                        'VENDOR' => 'test_vendor',
                        'USER' => 'test_user',
                        'PWD' => 'test_password',
                        'PARTNER' => 'test_partner',
                    ],
                    'responseData' => [
                        'RESULT' => '0',
                        'PNREF' => 'test_reference',
                    ],
                ],
                []
            ],
            'charge successful' => [
                [
                    'gatewayAction' => Option\Transaction::SALE,
                    'sourceTransactionData' => [
                        'reference' => 'test_reference',
                        'request' => [],
                    ],
                    'transactionData' => [
                        'action' => 'charge',
                        'request' => [],
                    ],
                    'configs' => [
                        Configuration::PAYPAL_PAYMENTS_PRO_PAYMENT_ACTION_KEY => 'charge',
                    ],
                    'requestOptions' => [
                        'VENDOR' => 'test_vendor',
                        'USER' => 'test_user',
                        'PWD' => 'test_password',
                        'PARTNER' => 'test_partner',
                    ],
                    'responseData' => [
                        'RESULT' => '0',
                        'PNREF' => 'test_reference',
                    ],
                ],
                []
            ],
            'delayed capture successful with enabled required amount' => [
                [
                    'gatewayAction' => Option\Transaction::DELAYED_CAPTURE,
                    'sourceTransactionData' => [],
                    'transactionData' => [
                        'action' => 'capture',
                        'request' => [],
                    ],
                    'configs' => [
                        Configuration::PAYPAL_PAYMENTS_PRO_PAYMENT_ACTION_KEY => 'authorize',
                        Configuration::PAYFLOW_GATEWAY_AUTHORIZATION_FOR_REQUIRED_AMOUNT_KEY => true
                    ],
                    'requestOptions' => [
                        'VENDOR' => 'test_vendor',
                        'USER' => 'test_user',
                        'PWD' => 'test_password',
                        'PARTNER' => 'test_partner',
                        'TENDER' => 'source_tender',
                        'ORIGID' => 'test_reference',
                    ],
                    'responseData' => [
                        'RESULT' => '0',
                        'PNREF' => 'test_reference',
                        'RESPMSG' => 'test_message',
                    ],
                ],
                []
            ],
            'delayed capture successful with disabled required amount' => [
                [
                    'gatewayAction' => Option\Transaction::DELAYED_CAPTURE,
                    'sourceTransactionData' => [
                        'reference' => 'test_reference',
                        'request' => ['TENDER' => 'source_tender'],
                    ],
                    'transactionData' => [
                        'action' => 'capture',
                        'request' => [],
                    ],
                    'configs' => [
                        Configuration::PAYPAL_PAYMENTS_PRO_PAYMENT_ACTION_KEY => 'authorize',
                        Configuration::PAYFLOW_GATEWAY_AUTHORIZATION_FOR_REQUIRED_AMOUNT_KEY => false
                    ],
                    'requestOptions' => [
                        'VENDOR' => 'test_vendor',
                        'USER' => 'test_user',
                        'PWD' => 'test_password',
                        'PARTNER' => 'test_partner',
                        'TENDER' => 'source_tender',
                        'ORIGID' => 'test_reference',
                    ],
                    'responseData' => [
                        'RESULT' => '0',
                        'PNREF' => 'test_reference',
                        'RESPMSG' => 'test_message',
                    ],
                ],
                [
                    'message' => 'test_message',
                    'successful' => true,
                ]
            ],
            'capture with amount required' => [
                [
                    'gatewayAction' => Option\Transaction::DELAYED_CAPTURE,
                    'sourceTransactionData' => [],
                    'transactionData' => [
                        'action' => 'capture',
                        'request' => [],
                    ],
                    'configs' => [
                        Configuration::PAYPAL_PAYMENTS_PRO_PAYMENT_ACTION_KEY => 'authorize',
                        Configuration::PAYFLOW_GATEWAY_AUTHORIZATION_FOR_REQUIRED_AMOUNT_KEY => true
                    ],
                    'requestOptions' => [
                        'VENDOR' => 'test_vendor',
                        'USER' => 'test_user',
                        'PWD' => 'test_password',
                        'PARTNER' => 'test_partner',
                        'TENDER' => 'C',
                        'AMT' => 0.0,
                        'CURRENCY' => null,
                        'ORIGID' => 'test_reference'
                    ],
                    'responseData' => [
                        'RESULT' => '0',
                        'PNREF' => 'test_reference',
                        'RESPMSG' => 'test_message',
                    ],
                ],
                []
            ],
            'capture without amount required' => [
                [
                    'gatewayAction' => Option\Transaction::SALE,
                    'sourceTransactionData' => [
                        'reference' => 'test_reference',
                        'request' => [],
                    ],
                    'transactionData' => [
                        'action' => 'capture',
                        'request' => [],
                    ],
                    'configs' => [
                        Configuration::PAYPAL_PAYMENTS_PRO_PAYMENT_ACTION_KEY => 'authorize',
                        Configuration::PAYFLOW_GATEWAY_AUTHORIZATION_FOR_REQUIRED_AMOUNT_KEY => false
                    ],
                    'requestOptions' => [
                        'VENDOR' => 'test_vendor',
                        'USER' => 'test_user',
                        'PWD' => 'test_password',
                        'PARTNER' => 'test_partner',
                        'TENDER' => 'C',
                        'AMT' => 0.0,
                        'CURRENCY' => null,
                        'ORIGID' => 'test_reference'
                    ],
                    'responseData' => [
                        'RESULT' => '0',
                        'PNREF' => 'test_reference',
                        'RESPMSG' => 'test_message',
                    ],
                ],
                []
            ],
            'capture without source' => [
                [
                    'gatewayAction' => Option\Transaction::DELAYED_CAPTURE,
                    'sourceTransactionData' => [],
                    'transactionData' => [
                        'action' => 'capture',
                        'request' => [],
                    ],
                    'configs' => [
                        Configuration::PAYPAL_PAYMENTS_PRO_PAYMENT_ACTION_KEY => 'authorize',
                    ],
                    'requestOptions' => [],
                    'responseData' => [],
                ],
                []
            ],
            'purchase successful' => [
                [
                    'gatewayAction' => Option\Transaction::SALE,
                    'sourceTransactionData' => [],
                    'transactionData' => [
                        'action' => 'purchase',
                        'request' => [],
                        'amount' => 100.0,
                        'currency' => 'USD',
                    ],
                    'configs' => [
                        Configuration::PAYPAL_PAYMENTS_PRO_PAYMENT_ACTION_KEY => 'charge',
                    ],
                    'requestOptions' => [
                        'VENDOR' => 'test_vendor',
                        'USER' => 'test_user',
                        'PWD' => 'test_password',
                        'PARTNER' => 'test_partner',
                        'CREATESECURETOKEN' => true,
                        'AMT' => 100.0,
                        'SILENTTRAN' => true,
                        'TENDER' => 'C',
                        'CURRENCY' => 'USD',
                        'RETURNURL' => 'orob2b_payment_callback_return',
                        'ERRORURL' => 'orob2b_payment_callback_error',
                    ],
                    'responseData' => [
                        'RESULT' => '0',
                        'PNREF' => 'test_reference',
                        'RESPMSG' => 'test_message',
                    ],
                ],
                [
                    'formAction' => 'test_form_action',
                ]
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param string $actionName
     *
     * @dataProvider supportsDataProvider
     */
    public function testSupports($expected, $actionName)
    {
        $this->assertEquals($expected, $this->method->supports($actionName));
    }

    /**
     * @return array
     */
    public function supportsDataProvider()
    {
        return [
            [true, PayPalPaymentsPro::AUTHORIZE],
            [true, PayPalPaymentsPro::CAPTURE],
            [true, PayPalPaymentsPro::CHARGE],
            [true, PayPalPaymentsPro::PURCHASE],
        ];
    }

    /**
     * @param bool $expected
     * @param bool $configValue
     *
     * @dataProvider validateSupportsDataProvider
     */
    public function testSupportsValidate($expected, $configValue)
    {
        $this->configureConfig([Configuration::PAYPAL_PAYMENTS_PRO_ZERO_AMOUNT_AUTHORIZATION_KEY => $configValue]);

        $this->assertEquals($expected, $this->method->supports(PayPalPaymentsPro::VALIDATE));
    }

    /**
     * @return array
     */
    public function validateSupportsDataProvider()
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}
