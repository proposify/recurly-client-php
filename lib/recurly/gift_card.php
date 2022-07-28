<?php

/**
 * Class Recurly_GiftCard
 * @property Recurly_Stub $gifter_account The URL of the Recurly_Account for the giver of the gift.
 * @property Recurly_Stub $recipient_account The URL of the Recurly_Account for the recipient of the gift.
 * @property Recurly_Stub $invoice The URL of the Recurly_Invoice for the invoice of the gift card.
 * @property Recurly_Stub $purchase_invoice The URL of the charge invoice for the gift card redemption.
 * @property Recurly_Stub $redemption_invoice The URL of the credit invoice for the gift card purchase.
 * @property Recurly_BillingInfo $billing_info
 * @property int $id The unique identifier of the gift card.
 * @property string $redemption_code The unique redemption code for the gift card, generated by Recurly. Will be 16 characters, alphanumeric, displayed uppercase, but accepted in any case at redemption. Used by the recipient_account to create a credit in the amount of the unit_amount_in_cents on their account.
 * @property int $balance_in_cents The remaining credit on the recipient_account associated with this gift card. Only shows once the gift card has been redeemed. Can be used to create gift card balance displays for your customers.
 * @property string $currency The currency of the unit_amount_in_cents and balance_in_cents.
 * @property string $product_code The product code or SKU of the gift card product.
 * @property int $unit_amount_in_cents The amount of the gift card, which is the amount of the charge to the gifter_account and the amount of credit that is applied to the recipient_account upon successful redemption.
 * @property Recurly_Delivery $delivery Block of delivery information.
 * @property DateTime $created_at The date and time the gift card was created in Recurly.
 * @property DateTime $updated_at The date and time the gift card was last updated.
 * @property DateTime $delivered_at When the gift card was sent to the recipient by Recurly via email, if method was email and the "Gift Card Delivery" email template was enabled. This will be empty for post delivery or email delivery where the email template was disabled.
 * @property DateTime $redeemed_at When the gift card was redeemed by the recipient.
 */
class Recurly_GiftCard extends Recurly_Resource
{
  /**
   * Get a gift card by the id
   *
   * @param string $giftCardId The gift card ID
   * @param Recurly_Client $client Optional client for the request, useful for mocking the client
   * @return object Recurly_Resource or null
   * @throws Recurly_Error
   */
  public static function get($giftCardId, $client = null) {
    return Recurly_Base::_get(Recurly_GiftCard::uriForGiftCard($giftCardId), $client);
  }

  /**
   * Redeem a gift card given an account code
   *
   * @param string $accountCode The account code
   *
   * @throws Recurly_Error
   */
  public function redeem($accountCode) {
    $doc = XmlTools::createDocument();
    $root = $doc->appendChild($doc->createElement('recipient_account'));
    $root->appendChild($doc->createElement('account_code', $accountCode));
    $uri = Recurly_GiftCard::uriForGiftCard($this->redemption_code) . '/redeem';

    $this->_save(Recurly_Client::POST, $uri, XmlTools::renderXML($doc));
  }

  public function create() {
    $this->_save(Recurly_Client::POST, Recurly_Client::PATH_GIFT_CARDS);
  }

  /**
   * Preview the creation and check for errors.
   *
   * Note: once preview() has been called you will not be able to call create()
   * without reassigning all the attributes.
   */
  public function preview() {
    $this->_save(Recurly_Client::POST, Recurly_Client::PATH_GIFT_CARDS . '/preview');
  }

  protected function uri() {
    if (!empty($this->_href))
      return $this->getHref();
    else
      return Recurly_GiftCard::uriForGiftCard($this->id);
  }

  protected static function uriForGiftCard($giftCardIdentifier) {
    return self::_safeUri(Recurly_Client::PATH_GIFT_CARDS, $giftCardIdentifier);
  }

  protected function getNodeName() {
    return 'gift_card';
  }
  protected function getWriteableAttributes() {
    if ($this->redemption_code) {
      return array('redemption_code');
    } else {
      return array(
        'product_code','unit_amount_in_cents','delivery',
        'gifter_account','currency',
        'billing_info'
      );
    }
  }
}
