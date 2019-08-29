<?php
/**
 * Class Payment
 * @author 卢绍明<lusm@sz-bcs.com.cn>
 * @date   2019/8/29
 */
namespace Shannon\PayPal;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

class Payment
{
    private $items = array();
    private $currency = 'USD';
    private $productTotal = 0;
    private $shippingFee = 0;
    private $tax = 0;
    private $discount = 0;
    private $orderTotal = 0;
    private $orderNo;
    private $shipping;
    private $billing;
    private $payer;
    private $itemList;
    private $amount;
    private $detail;
    private $transaction;
    private $redirectUrl;
    private $applicationContext;

    public function init($data)
    {
        if (isset($data['products'])) $this->setItems($data['products']);

        if (isset($data['product_total'])) $this->setProductTotal($data['product_total']);

        if (isset($data['currency']))  $this->setCurrency($data['currency']);

        if (isset($data['tax'])) $this->setTax($data['tax']);

        $this->setOrderTotal(isset($data['order_total']) ? $data['order_total'] : 0);

        if (isset($data['shipping_fee'])) $this->setShippingFee($data['shipping_fee']);

        if (isset($data['discount'])) $this->setDiscount($data['discount']);

        if (isset($data['order_no'])) $this->setOrderNo($data['order_no']);

        if (isset($data['billing'])) $this->setBilling($data['billing']);

        if (isset($data['shipping'])) $this->setShipping($data['shipping']);

        if (isset($data['payer'])) $this->setPayer($data['payer']);

        if (isset($data['redirect_url'])) $this->setRedirectUrl($data['redirect_url']);

        if (isset($data['application_context'])) $this->setApplicationContext($data['application_context']);

        return $this;
    }

    public function setItems($products)
    {
        foreach ($products as $k => $product) {
            $this->items[$k] = new Item();
            $this->items[$k]->setSku($product['sku'])
                ->setName($product['name'])
                ->setCurrency($this->currency)
                ->setQuantity($product['qty'])
                ->setPrice($product['price']) // setPrice()：单价
                ->setTax($product['line_tax']);
            $this->productTotal += $product['line_total'];
        }
    }

    public function setApplicationContext($applicationContext)
    {
        $this->applicationContext = $applicationContext;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function setTax($tax)
    {
        $this->tax = $tax;
    }

    public function setShippingFee($shippingFee)
    {
        $this->shippingFee = $shippingFee;
    }

    public function setDiscount($discount)
    {
        if (is_array($discount)) {
            $value = $discount['total'];
            $name = $discount['name'];
        } elseif (is_numeric($discount)) {
            $name = 'Discount';
            $value = $discount;
        } else {
            throw new ShannonPaypalException('Param discount is invalid');
        }

        $this->discount = $value;
        $discountItem = new Item();
        $discountItem->setName($name)
            ->setCurrency($this->currency)
            ->setQuantity(1)
            ->setPrice($value) // setPrice()：单价
            ->setTax(0);
        $this->productTotal += $discount;
        $this->items[] = $discountItem;
    }

    public function setProductTotal($total)
    {
        $this->productTotal = $total;
    }

    public function setOrderTotal($total)
    {
        $this->orderTotal = $total;
    }

    public function setOrderNo($no)
    {
        $this->orderNo = $no;
    }

    public function getOrderNo()
    {
        if (empty($this->orderNo)) $this->orderNo = Core::createOrderNo();
        return $this->orderNo;
    }

    public function setShipping($shipping)
    {
        $s = new ShippingAddress();
        $s->setShippingAddress($shipping);
        $this->shipping =  $s->getShippingAddress();
    }

    public function setBilling($billing)
    {
        $s = new BillingAddress();
        $s->setBillingAddress($billing);
        $this->billing = $s->getBillingAddress();
    }

    public function setPayer($payer)
    {
        $payerInfo = new PayerInfo();
        $p = $payerInfo->setPayerInfo($payer, $this->shipping, $this->billing);
        $this->payer = $payerInfo->setPayer(null, $p, 'paypal');
    }

    public function setItemList(array $item, $shippingAddress)
    {
        $itemList = new ItemList();
        if (count($item) > 0) $itemList->setItems($item);
        if ($shippingAddress) $itemList->setShippingAddress($shippingAddress);
        $this->itemList = $itemList;
    }

    public function setDetail(float $subTotal)
    {
        $detail = new Details();
        // 运费，增值税
        $detail->setShipping($this->shippingFee)->setTax($this->tax)
            ->setSubtotal($subTotal);
        $this->detail = $detail;
    }


    public function setAmount($detail, float $total)
    {
        if (empty($total)) throw new ShannonPaypalException('order_total is invalid');
        $amount = new Amount();
        $amount->setCurrency($this->currency)
            ->setTotal($total);// setTotal(): 订单总价，包含所有费用
        if (!is_null($detail)) $amount->setDetails($detail);
        $this->amount = $amount;
    }

    public function setTransaction(Amount $amount, $itemList, string $orderNo)
    {
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setInvoiceNumber($orderNo);
        if (!is_null($itemList)) $transaction->setItemList($itemList);
        $this->transaction = $transaction;
    }

    public function setRedirectUrl($redirect)
    {
        $redirectUrl = new RedirectUrls();
        $redirectUrl->setReturnUrl($redirect['success'])
            ->setCancelUrl($redirect['cancel']);
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @param Payer $payer
     * @param Transaction $transaction
     * @param RedirectUrls $redirectUrl
     * @param string $paymentAction default sale
     * @return \PayPal\Api\Payment
     */
    public function getPayment(Payer $payer, Transaction $transaction, RedirectUrls $redirectUrl, $paymentAction = 'sale') : \PayPal\Api\Payment
    {
        $payment = new \PayPal\Api\Payment();
        $payment->setIntent($paymentAction)
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrl)
            ->setTransactions([$transaction]);
        return $payment;
    }

    public function create($config = [])
    {
        $paypal = ApiContext::getInstance()->createContext($config);

        $this->setItemList($this->items, $this->shipping);

        $this->setDetail($this->productTotal);

        $this->setAmount($this->detail, $this->orderTotal);

        $orderNo = $this->getOrderNo();

        $this->setTransaction($this->amount, $this->itemList, $orderNo);

        $payment = $this->getPayment($this->payer, $this->transaction, $this->redirectUrl);

        if ($this->applicationContext) {
            $payment->setApplicationContext($this->applicationContext);
        }
        $payment->create($paypal);

        return $payment;
    }

    public function receiptPayment($paymentId, $payerId)
    {
        $paypal = ApiContext::getInstance()->createContext();
        $paymentExecute = new PaymentExecution();
        $paymentExecute->setPayerId($payerId);
        $payment = new \PayPal\Api\Payment();
        $payment->setId($paymentId)->execute($paymentExecute, $paypal);

        // 获取交易ID，可用于退款操作
        $transaction = $payment->getTransactions();
        $transactionId = $transaction[0]->related_resources[0]->sale->id;
        return $transactionId;
    }
}