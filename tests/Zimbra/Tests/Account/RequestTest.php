<?php

namespace Zimbra\Tests\Account;

use Zimbra\Tests\ZimbraTestCase;

use Zimbra\Enum\AccountBy;
use Zimbra\Enum\AceRightType;
use Zimbra\Enum\ConditionOperator as CondOp;
use Zimbra\Enum\ContentType;
use Zimbra\Enum\DistributionListBy as DLBy;
use Zimbra\Enum\DistributionListGranteeBy as DLGranteeBy;
use Zimbra\Enum\DistributionListSubscribeOp as DLSubscribeOp;
use Zimbra\Enum\GalSearchType as SearchType;
use Zimbra\Enum\GranteeType;
use Zimbra\Enum\MemberOfSelector as MemberOf;
use Zimbra\Enum\Operation;
use Zimbra\Enum\SortBy;
use Zimbra\Enum\TargetType;
use Zimbra\Enum\TargetBy;
use Zimbra\Enum\ZimletStatus;

/**
 * Testcase class for account request.
 */
class RequestTest extends ZimbraTestCase
{
    public function testAuth()
    {
        $account = new \Zimbra\Struct\AccountSelector(AccountBy::NAME(), 'value');
        $preauth = new \Zimbra\Account\Struct\PreAuth(1000, 'value', 1000);
        $authToken = new \Zimbra\Account\Struct\AuthToken('value', true);

        $attr = new \Zimbra\Account\Struct\Attr('name', 'value', true);
        $attrs = new \Zimbra\Account\Struct\AuthAttrs(array($attr));

        $pref = new \Zimbra\Account\Struct\Pref('name', 'value', 1000);
        $prefs = new \Zimbra\Account\Struct\AuthPrefs(array($pref));

        $req = new \Zimbra\Account\Request\Auth(
            $account, 'password', $preauth, $authToken, 'virtualHost',
            $prefs, $attrs, 'requestedSkin', false
        );
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($account, $req->account());
        $this->assertSame('password', $req->password());
        $this->assertSame($preauth, $req->preauth());
        $this->assertSame($authToken, $req->authToken());
        $this->assertSame('virtualHost', $req->virtualHost());
        $this->assertSame($prefs, $req->prefs());
        $this->assertSame($attrs, $req->attrs());
        $this->assertSame('requestedSkin', $req->requestedSkin());
        $this->assertFalse($req->persistAuthTokenCookie());

        $req->account($account)
            ->password('password')
            ->preauth($preauth)
            ->authToken($authToken)
            ->virtualHost('virtualHost')
            ->prefs($prefs)
            ->attrs($attrs)
            ->requestedSkin('requestedSkin')
            ->persistAuthTokenCookie(true);
        $this->assertSame($account, $req->account());
        $this->assertSame('password', $req->password());
        $this->assertSame($preauth, $req->preauth());
        $this->assertSame($authToken, $req->authToken());
        $this->assertSame('virtualHost', $req->virtualHost());
        $this->assertSame($prefs, $req->prefs());
        $this->assertSame($attrs, $req->attrs());
        $this->assertSame('requestedSkin', $req->requestedSkin());
        $this->assertTrue($req->persistAuthTokenCookie());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<AuthRequest persistAuthTokenCookie="true">'
                .'<account by="name">value</account>'
                .'<password>password</password>'
                .'<preauth timestamp="1000" expiresTimestamp="1000">value</preauth>'
                .'<authToken verifyAccount="true">value</authToken>'
                .'<virtualHost>virtualHost</virtualHost>'
                .'<prefs>'
                    .'<pref name="name" modified="1000">value</pref>'
                .'</prefs>'
                .'<attrs>'
                    .'<attr name="name" pd="true">value</attr>'
                .'</attrs>'
                .'<requestedSkin>requestedSkin</requestedSkin>'
            .'</AuthRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'AuthRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'account' => array(
                    'by' => 'name',
                    '_content' => 'value',
                ),
                'password' => 'password',
                'preauth' => array(
                    'timestamp' => 1000,
                    'expiresTimestamp' => 1000,
                    '_content' => 'value',
                ),
                'authToken' => array(
                    'verifyAccount' => true,
                    '_content' => 'value',
                ),
                'virtualHost' => 'virtualHost',
                'prefs' => array(
                    'pref' => array(
                        array(
                            'name' => 'name',
                            'modified' => 1000,
                            '_content' => 'value',
                        ),
                    ),
                ),
                'attrs' => array(
                    'attr' => array(
                        array(
                            'name' => 'name',
                            'pd' => true,
                            '_content' => 'value',
                        ),
                    ),
                ),
                'requestedSkin' => 'requestedSkin',
                'persistAuthTokenCookie' => true,
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testAutoCompleteGal()
    {
        $req = new \Zimbra\Account\Request\AutoCompleteGal('name', true, SearchType::ALL(), 'id', 100);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame('name', $req->name());
        $this->assertTrue($req->needExp());
        $this->assertSame('all', $req->type()->value());
        $this->assertSame('id', $req->galAcctId());
        $this->assertSame(100, $req->limit());

        $req->name('name')
            ->needExp(false)
            ->type(SearchType::ACCOUNT())
            ->galAcctId('galAcctId')
            ->limit(10);
        $this->assertSame('name', $req->name());
        $this->assertFalse($req->needExp());
        $this->assertSame('account', $req->type()->value());
        $this->assertSame('galAcctId', $req->galAcctId());
        $this->assertSame(10, $req->limit());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<AutoCompleteGalRequest '
                .'needExp="false" name="name" type="account" galAcctId="galAcctId" limit="10" />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'AutoCompleteGalRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'name' => 'name',
                'needExp' => false,
                'type' => 'account',
                'galAcctId' =>'galAcctId',
                'limit' => 10,
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testBaseRequest()
    {
        $req = $this->getMockForAbstractClass('\Zimbra\Account\Request\Base');
        $this->assertInstanceOf('Zimbra\Soap\Request', $req);
        $this->assertEquals('urn:zimbraAccount', $req->xmlNamespace());

        $req = $this->getMockForAbstractClass('\Zimbra\Account\Request\BaseAttr');
        $this->assertInstanceOf('Zimbra\Soap\Request', $req);
        $this->assertEquals('urn:zimbraAccount', $req->xmlNamespace());
    }

    public function testChangePassword()
    {
        $account = new \Zimbra\Struct\AccountSelector(AccountBy::NAME(), 'value');
        $req = new \Zimbra\Account\Request\ChangePassword(
            $account, 'oldPassword', 'password', 'virtualHost'
        );
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $this->assertSame($account, $req->account());
        $this->assertSame('oldPassword', $req->oldPassword());
        $this->assertSame('password', $req->password());
        $this->assertSame('virtualHost', $req->virtualHost());

        $req->account($account)
            ->oldPassword('oldPassword')
            ->password('password')
            ->virtualHost('virtualHost');

        $this->assertSame($account, $req->account());
        $this->assertSame('oldPassword', $req->oldPassword());
        $this->assertSame('password', $req->password());
        $this->assertSame('virtualHost', $req->virtualHost());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<ChangePasswordRequest>'
                .'<account by="name">value</account>'
                .'<oldPassword>oldPassword</oldPassword>'
                .'<password>password</password>'
                .'<virtualHost>virtualHost</virtualHost>'
            .'</ChangePasswordRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'ChangePasswordRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'account' => array(
                    'by' => 'name',
                    '_content' => 'value',
                ),
                'oldPassword' => 'oldPassword',
                'password' =>'password',
                'virtualHost' => 'virtualHost',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testCheckRights()
    {
        $target = new \Zimbra\Account\Struct\CheckRightsTargetSpec(
            TargetType::DOMAIN(), TargetBy::ID(), 'key', array('right1', 'right2')
        );
        $req = new \Zimbra\Account\Request\CheckRights(array($target));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame(array($target), $req->target()->all());

        $req->addTarget($target);
        $this->assertSame(array($target, $target), $req->target()->all());
        $req->target()->remove(1);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<CheckRightsRequest>'
                .'<target type="domain" by="id" key="key">'
                    .'<right>right1</right>'
                    .'<right>right2</right>'
                .'</target>'
            .'</CheckRightsRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'CheckRightsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'target' => array(
                    array(
                        'type' => 'domain',
                        'by' => 'id',
                        'key' => 'key',
                        'right' => array(
                            'right1',
                            'right2',
                        ),
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testCreateDistributionList()
    {
        $attr = new \Zimbra\Struct\KeyValuePair('key', 'value');
        $req = new \Zimbra\Account\Request\CreateDistributionList('name', false, array($attr));        
        $this->assertInstanceOf('Zimbra\Account\Request\BaseAttr', $req);
        $this->assertSame('name', $req->name());
        $this->assertFalse($req->dynamic());

        $req->name('name')
            ->dynamic(true);
        $this->assertSame('name', $req->name());
        $this->assertTrue($req->dynamic());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<CreateDistributionListRequest name="name" dynamic="true">'
                .'<a n="key">value</a>'
            .'</CreateDistributionListRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'CreateDistributionListRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'name' => 'name',
                'dynamic' => true,
                'a' => array(
                    array('n' => 'key', '_content' => 'value'),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testCreateIdentity()
    {
        $attr = new \Zimbra\Account\Struct\Attr('name', 'value', true);
        $identity = new \Zimbra\Account\Struct\Identity('name', 'id', array($attr));

        $req = new \Zimbra\Account\Request\CreateIdentity($identity);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($identity, $req->identity());

        $req->identity($identity);
        $this->assertSame($identity, $req->identity());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<CreateIdentityRequest>'
                .'<identity name="name" id="id">'
                    .'<a name="name" pd="true">value</a>'
                .'</identity>'
            .'</CreateIdentityRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'CreateIdentityRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'identity' => array(
                    'name' => 'name',
                    'id' => 'id',
                    'a' => array(
                        array('name' => 'name', 'pd' => true, '_content' => 'value'),
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testCreateSignature()
    {
        $content = new \Zimbra\Account\Struct\SignatureContent('value', ContentType::TEXT_PLAIN());
        $signature = new \Zimbra\Account\Struct\Signature('name', 'id', 'cid', array($content));

        $req = new \Zimbra\Account\Request\CreateSignature($signature);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($signature, $req->signature());

        $req->signature($signature);
        $this->assertSame($signature, $req->signature());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<CreateSignatureRequest>'
                .'<signature id="id" name="name">'
                    .'<cid>cid</cid>'
                    .'<content type="text/plain">value</content>'
                .'</signature>'
            .'</CreateSignatureRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'CreateSignatureRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'signature' => array(
                    'name' => 'name',
                    'id' => 'id',
                    'cid' => 'cid',
                    'content' => array(
                        array('type' => 'text/plain', '_content' => 'value'),
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testDeleteIdentity()
    {
        $identity = new \Zimbra\Account\Struct\NameId('name', 'id');
        $req = new \Zimbra\Account\Request\DeleteIdentity($identity);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($identity, $req->identity());

        $req->identity($identity);
        $this->assertSame($identity, $req->identity());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<DeleteIdentityRequest>'
                .'<identity name="name" id="id" />'
            .'</DeleteIdentityRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'DeleteIdentityRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'identity' => array(
                    'name' => 'name',
                    'id' => 'id',
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testDeleteSignature()
    {
        $signature = new \Zimbra\Account\Struct\NameId('name', 'id');
        $req = new \Zimbra\Account\Request\DeleteSignature($signature);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($signature, $req->signature());

        $req->signature($signature);
        $this->assertSame($signature, $req->signature());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<DeleteSignatureRequest>'
                .'<signature name="name" id="id" />'
            .'</DeleteSignatureRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'DeleteSignatureRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'signature' => array(
                    'name' => 'name',
                    'id' => 'id',
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testDiscoverRights()
    {
        $req = new \Zimbra\Account\Request\DiscoverRights(array('right1', 'right2'));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame(array('right1', 'right2'), $req->right()->all());

        $req->addRight('right3');
        $this->assertSame(array('right1', 'right2', 'right3'), $req->right()->all());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<DiscoverRightsRequest>'
                .'<right>right1</right>'
                .'<right>right2</right>'
                .'<right>right3</right>'
            .'</DiscoverRightsRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'DiscoverRightsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'right' => array(
                    'right1',
                    'right2',
                    'right3',
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testDistributionListAction()
    {
        $subsReq = new \Zimbra\Account\Struct\DistributionListSubscribeReq(DLSubscribeOp::SUBSCRIBE(), 'value', true);
        $owner = new \Zimbra\Account\Struct\DistributionListGranteeSelector(GranteeType::USR(), DLGranteeBy::ID(), 'value');
        $grantee = new \Zimbra\Account\Struct\DistributionListGranteeSelector(GranteeType::ALL(), DLGranteeBy::NAME(), 'value');
        $right = new \Zimbra\Account\Struct\DistributionListRightSpec('right', array($grantee));
        $a = new \Zimbra\Struct\KeyValuePair('key', 'value');
        $action = new \Zimbra\Account\Struct\DistributionListAction(Operation::MODIFY(), 'newName', $subsReq, array('dlm'), array($owner), array($right), array($a));

        $dl = new \Zimbra\Account\Struct\DistributionListSelector(DLBy::NAME(), 'value');
        $attr = new \Zimbra\Account\Struct\Attr('name', 'value', true);

        $req = new \Zimbra\Account\Request\DistributionListAction($dl, $action, array($attr));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($dl, $req->dl());
        $this->assertSame($action, $req->action());
        $this->assertSame(array($attr), $req->attr()->all());

        $req->dl($dl)
            ->action($action)
            ->addAttr($attr);
        $this->assertSame($dl, $req->dl());
        $this->assertSame($action, $req->action());
        $this->assertSame(array($attr, $attr), $req->attr()->all());
        $req->attr()->remove(1);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<DistributionListActionRequest>'
                .'<dl by="name">value</dl>'
                .'<action op="modify">'
                    .'<newName>newName</newName>'
                    .'<subsReq op="subscribe" bccOwners="true">value</subsReq>'
                    .'<a n="key">value</a>'
                    .'<dlm>dlm</dlm>'
                    .'<owner type="usr" by="id">value</owner>'
                    .'<right right="right">'
                        .'<grantee type="all" by="name">value</grantee>'
                    .'</right>'
                .'</action>'
                .'<a name="name" pd="true">value</a>'
            .'</DistributionListActionRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'DistributionListActionRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'dl' => array(
                    'by' => 'name',
                    '_content' => 'value',
                ),
                'action' => array(
                    'op' => 'modify',
                    'newName' => 'newName',
                    'subsReq' => array(
                        'op' => 'subscribe',
                        '_content' => 'value',
                        'bccOwners' => true,
                    ),
                    'dlm' => array('dlm'),
                    'owner' => array(
                        array(
                            'type' => 'usr',
                            '_content' => 'value',
                            'by' => 'id',
                        ),
                    ),
                    'right' => array(
                        array(
                            'right' => 'right',
                            'grantee' => array(
                                array(
                                    'type' => 'all',
                                    '_content' => 'value',
                                    'by' => 'name',
                                ),
                            ),
                        ),
                    ),
                    'a' => array(
                        array(
                            'n' => 'key',
                            '_content' => 'value',
                        ),
                    ),
                ),
                'a' => array(
                    array(
                        'name' => 'name',
                        'pd' => '1',
                        '_content' => 'value',
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testEndSession()
    {
        $req = new \Zimbra\Account\Request\EndSession;
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<EndSessionRequest />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'EndSessionRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetAccountDistributionLists()
    {
        $req = new \Zimbra\Account\Request\GetAccountDistributionLists(false, MemberOf::DIRECT_ONLY(), 'attr');
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertFalse($req->ownerOf());
        $this->assertSame('directOnly', $req->memberOf()->value());
        $this->assertSame('attr', $req->attrs());

        $req->ownerOf(true)
            ->memberOf(MemberOf::ALL())
            ->attrs('attrs');
        $this->assertTrue($req->ownerOf());
        $this->assertSame('all', $req->memberOf()->value());
        $this->assertSame('attrs', $req->attrs());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetAccountDistributionListsRequest ownerOf="true" memberOf="all" attrs="attrs" />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetAccountDistributionListsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'ownerOf' => true,
                'memberOf' => 'all',
                'attrs' => 'attrs',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetAccountInfo()
    {
        $account = new \Zimbra\Struct\AccountSelector(AccountBy::NAME(), 'value');

        $req = new \Zimbra\Account\Request\GetAccountInfo($account);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($account, $req->account());

        $req->account($account);
        $this->assertSame($account, $req->account());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetAccountInfoRequest>'
                .'<account by="name">value</account>'
            .'</GetAccountInfoRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetAccountInfoRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'account' => array(
                    'by' => 'name',
                    '_content' => 'value',
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetAllLocales()
    {
        $req = new \Zimbra\Account\Request\GetAllLocales;
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetAllLocalesRequest />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetAllLocalesRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetAvailableCsvFormats()
    {
        $req = new \Zimbra\Account\Request\GetAvailableCsvFormats;
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetAvailableCsvFormatsRequest />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetAvailableCsvFormatsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetAvailableLocales()
    {
        $req = new \Zimbra\Account\Request\GetAvailableLocales;
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetAvailableLocalesRequest />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetAvailableLocalesRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetAvailableSkins()
    {
        $req = new \Zimbra\Account\Request\GetAvailableSkins;
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetAvailableSkinsRequest />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetAvailableSkinsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetDistributionList()
    {
        $dl = new \Zimbra\Account\Struct\DistributionListSelector(DLBy::NAME(), 'value');
        $attr = new \Zimbra\Account\Struct\Attr('name', 'value', true);
        $req = new \Zimbra\Account\Request\GetDistributionList($dl, false, 'sendToDistList', array($attr));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $this->assertSame($dl, $req->dl());
        $this->assertFalse($req->needOwners());
        $this->assertSame('sendToDistList', $req->needRights());
        $this->assertSame(array($attr), $req->attr()->all());

        $req->dl($dl)
            ->needOwners(true)
            ->needRights('sendToDistList,viewDistList')
            ->addAttr($attr);
        $this->assertSame($dl, $req->dl());
        $this->assertTrue($req->needOwners());
        $this->assertSame('sendToDistList,viewDistList', $req->needRights());
        $this->assertSame(array($attr, $attr), $req->attr()->all());
        $req->attr()->remove(1);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetDistributionListRequest needOwners="true" needRights="sendToDistList,viewDistList">'
                .'<dl by="name">value</dl>'
                .'<a name="name" pd="true">value</a>'
            .'</GetDistributionListRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetDistributionListRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'needOwners' => true,
                'needRights' => 'sendToDistList,viewDistList',
                'dl' => array(
                    'by' => 'name',
                    '_content' => 'value',
                ),
                'a' => array(
                    array('name' => 'name', 'pd' => true, '_content' => 'value'),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function getGetDistributionListMembers()
    {
        $req = new \Zimbra\Account\Request\GetDistributionListMembers('dl', 100, 100);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame('dl', $req->dl());
        $this->assertSame(100, $req->limit());
        $this->assertSame(100, $req->offset());

        $req->dl('name')
            ->limit(10)
            ->offset(10);
        $this->assertSame('name', $req->dl());
        $this->assertSame(10, $req->limit());
        $this->assertSame(10, $req->offset());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetDistributionListMembersRequest limit="10" offset="10">'
                .'<dl>name</dl>'
            .'</GetDistributionListRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetDistributionListRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'dl' => 'name',
                'limit' => 10,
                'offset' => 10,
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetIdentities()
    {
        $req = new \Zimbra\Account\Request\GetIdentities();
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetIdentitiesRequest />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetIdentitiesRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function getGetInfo()
    {
        $req = new \Zimbra\Account\Request\GetInfo('a,mbox,b,prefs,c', 'rights');
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame('mbox,prefs', $req->sections());
        $this->assertSame('rights', $req->rights());

        $req->sections('x,attrs,y,zimlets,z')
            ->rights('rights');
        $this->assertSame('attrs,zimlets', $req->sections());
        $this->assertSame('rights', $req->rights());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetInfoRequest sections="attrs,zimlets" rights="rights" />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetInfoRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'sections' => 'attrs,zimlets',
                'rights' => 'rights',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function getGetPrefs()
    {
        $pref = new \Zimbra\Account\Struct\Pref('name', 'value', 1000);
        $req = new \Zimbra\Account\Request\GetPrefs(array($pref));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame(array($pref), $req->pref()->all());

        $req->addPref($pref);
        $this->assertSame(array($pref, $pref), $req->pref()->all());
        $req->pref()->remove(1);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetPrefsRequest>'
                .'<pref name="name" modified="1000">value</pref>'
            .'</GetPrefsRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetPrefsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'pref' => array(
                    array(
                        'name' => 'name', 'modified' => 1000, '_content' => 'value',
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetRights()
    {
        $ace = new \Zimbra\Account\Struct\Right('right');
        $req = new \Zimbra\Account\Request\GetRights(array($ace));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame(array($ace), $req->ace()->all());

        $req->addAce($ace);
        $this->assertSame(array($ace, $ace), $req->ace()->all());
        $req->ace()->remove(1);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetRightsRequest>'
                .'<ace right="right" />'
            .'</GetRightsRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetRightsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'ace' => array(
                    array('right' => 'right'),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetShareInfo()
    {
        $owner = new \Zimbra\Struct\AccountSelector(AccountBy::NAME(), 'value');
        $grantee = new \Zimbra\Struct\GranteeChooser('type', 'id', 'name');

        $req = new \Zimbra\Account\Request\GetShareInfo($grantee, $owner, true, false);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($grantee, $req->grantee());
        $this->assertSame($owner, $req->owner());
        $this->assertTrue($req->internal());
        $this->assertFalse($req->includeSelf());

        $req->grantee($grantee)
            ->owner($owner)
            ->internal(false)
            ->includeSelf(true);
        $this->assertSame($grantee, $req->grantee());
        $this->assertSame($owner, $req->owner());
        $this->assertFalse($req->internal());
        $this->assertTrue($req->includeSelf());


        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetShareInfoRequest internal="false" includeSelf="true" >'
                .'<grantee type="type" id="id" name="name" />'
                .'<owner by="name">value</owner>'
            .'</GetShareInfoRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetShareInfoRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'internal' => false,
                'includeSelf' => true,
                'grantee' => array(
                    'type' => 'type',
                    'id' => 'id',
                    'name' => 'name',
                ),
                'owner' => array(
                    'by' => 'name',
                    '_content' => 'value',
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetSignatures()
    {
        $req = new \Zimbra\Account\Request\GetSignatures;
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetSignaturesRequest />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetSignaturesRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetVersionInfo()
    {
        $req = new \Zimbra\Account\Request\GetVersionInfo;
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetVersionInfoRequest />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetVersionInfoRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGetWhiteBlackList()
    {
        $req = new \Zimbra\Account\Request\GetWhiteBlackList;
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GetWhiteBlackListRequest />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GetWhiteBlackListRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testGrantRights()
    {
        $ace = new \Zimbra\Account\Struct\AccountACEInfo(
            GranteeType::ALL(), AceRightType::VIEW_FREE_BUSY(), 'zid', 'dir', 'key', 'pw', true, false
        );
        $req = new \Zimbra\Account\Request\GrantRights(array($ace));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame(array($ace), $req->ace()->all());

        $req->addAce($ace);
        $this->assertSame(array($ace, $ace), $req->ace()->all());
        $req->ace()->remove(1);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<GrantRightsRequest>'
                .'<ace gt="all" right="viewFreeBusy" zid="zid" d="dir" key="key" pw="pw" deny="true" chkgt="false" />'
            .'</GrantRightsRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'GrantRightsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'ace' => array(
                    array(
                        'gt' => 'all',
                        'right' => 'viewFreeBusy',
                        'zid' => 'zid',
                        'd' => 'dir',
                        'key' => 'key',
                        'pw' => 'pw',
                        'deny' => true,
                        'chkgt' => false,
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testModifyIdentity()
    {
        $attr = new \Zimbra\Account\Struct\Attr('name', 'value', true);
        $identity = new \Zimbra\Account\Struct\Identity('name', 'id', array($attr));

        $req = new \Zimbra\Account\Request\ModifyIdentity($identity);    
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($identity, $req->identity());
        $req->identity($identity);
        $this->assertSame($identity, $req->identity());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<ModifyIdentityRequest>'
                .'<identity name="name" id="id">'
                    .'<a name="name" pd="true">value</a>'
                .'</identity>'
            .'</ModifyIdentityRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'ModifyIdentityRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'identity' => array(
                    'name' => 'name',
                    'id' => 'id',
                    'a' => array(
                        array(
                            'name' => 'name',
                            '_content' => 'value',
                            'pd' => true,
                        ),
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testModifyPrefs()
    {
        $pref = new \Zimbra\Account\Struct\Pref('name', 'value', 1000);
        $req = new \Zimbra\Account\Request\ModifyPrefs(array($pref));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame(array($pref), $req->pref()->all());

        $req->addPref($pref);
        $this->assertSame(array($pref, $pref), $req->pref()->all());
        $req->pref()->remove(1);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<ModifyPrefsRequest>'
                .'<pref name="name" modified="1000">value</pref>'
            .'</ModifyPrefsRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'ModifyPrefsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'pref' => array(
                    array(
                        'name' => 'name', 'modified' => 1000, '_content' => 'value',
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testModifyProperties()
    {
        $prop = new \Zimbra\Account\Struct\Prop('zimlet', 'name', 'value');
        $req = new \Zimbra\Account\Request\ModifyProperties(array($prop));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame(array($prop), $req->prop()->all());

        $req->addProp($prop);
        $this->assertSame(array($prop, $prop), $req->prop()->all());
        $req->prop()->remove(1);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<ModifyPropertiesRequest>'
                .'<prop zimlet="zimlet" name="name">value</prop>'
            .'</ModifyPropertiesRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'ModifyPropertiesRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'prop' => array(
                    array(
                        'zimlet' => 'zimlet', 'name' => 'name', '_content' => 'value',
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testModifySignature()
    {
        $content = new \Zimbra\Account\Struct\SignatureContent('value', ContentType::TEXT_HTML());
        $signature = new \Zimbra\Account\Struct\Signature('name', 'id', 'cid', array($content));

        $req = new \Zimbra\Account\Request\ModifySignature($signature);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($signature, $req->signature());
        $req->signature($signature);
        $this->assertSame($signature, $req->signature());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<ModifySignatureRequest>'
                .'<signature name="name" id="id">'
                    .'<cid>cid</cid>'
                    .'<content type="text/html">value</content>'
                .'</signature>'
            .'</ModifySignatureRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'ModifySignatureRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'signature' => array(
                    'name' => 'name',
                    'id' => 'id',
                    'cid' => 'cid',
                    'content' => array(
                        array(
                            'type' => 'text/html',
                            '_content' => 'value',
                        ),
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testModifyWhiteBlackList()
    {
        $white = new \Zimbra\Struct\OpValue('+', 'white');
        $black = new \Zimbra\Struct\OpValue('-', 'black');
        $whiteList = new \Zimbra\Account\Struct\WhiteList(array($white));
        $blackList = new \Zimbra\Account\Struct\BlackList(array($black));

        $req = new \Zimbra\Account\Request\ModifyWhiteBlackList($whiteList, $blackList);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($whiteList, $req->whiteList());
        $this->assertSame($blackList, $req->blackList());

        $req->whiteList($whiteList)
            ->blackList($blackList);
        $this->assertSame($whiteList, $req->whiteList());
        $this->assertSame($blackList, $req->blackList());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<ModifyWhiteBlackListRequest>'
                .'<whiteList>'
                    .'<addr op="+">white</addr>'
                .'</whiteList>'
                .'<blackList>'
                    .'<addr op="-">black</addr>'
                .'</blackList>'
            .'</ModifyWhiteBlackListRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'ModifyWhiteBlackListRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'whiteList' => array(
                    'addr' => array(
                        array(
                            'op' => '+',
                            '_content' => 'white',
                        ),
                    ),
                ),
                'blackList' => array(
                    'addr' => array(
                        array(
                            'op' => '-',
                            '_content' => 'black',
                        ),
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testModifyZimletPrefs()
    {
        $zimlet = new \Zimbra\Account\Struct\ZimletPrefsSpec('name', ZimletStatus::ENABLED());
        $req = new \Zimbra\Account\Request\ModifyZimletPrefs(array($zimlet));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame(array($zimlet), $req->zimlet()->all());

        $req->addZimlet($zimlet);
        $this->assertSame(array($zimlet, $zimlet), $req->zimlet()->all());
        $req->zimlet()->remove(1);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<ModifyZimletPrefsRequest>'
                .'<zimlet name="name" presence="enabled" />'
            .'</ModifyZimletPrefsRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'ModifyZimletPrefsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'zimlet' => array(
                    array(
                        'name' => 'name',
                        'presence' => 'enabled',
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testRevokeRights()
    {
        $ace = new \Zimbra\Account\Struct\AccountACEInfo(GranteeType::ALL(), AceRightType::VIEW_FREE_BUSY(), 'zid', 'dir', 'key', 'pw', true, false);
        $req = new \Zimbra\Account\Request\RevokeRights(array($ace));
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame(array($ace), $req->ace()->all());

        $req->addAce($ace);
        $this->assertSame(array($ace, $ace), $req->ace()->all());
        $req->ace()->remove(1);

        $xml = '<?xml version="1.0"?>'."\n"
            .'<RevokeRightsRequest>'
                .'<ace gt="all" right="viewFreeBusy" zid="zid" d="dir" key="key" pw="pw" deny="true" chkgt="false" />'
            .'</RevokeRightsRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'RevokeRightsRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'ace' => array(
                    array(
                        'gt' => 'all',
                        'right' => 'viewFreeBusy',
                        'zid' => 'zid',
                        'd' => 'dir',
                        'key' => 'key',
                        'pw' => 'pw',
                        'deny' => true,
                        'chkgt' => false,
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testSearchCalendarResources()
    {
        $cursor = new \Zimbra\Struct\CursorInfo('id','sortVal', 'endSortVal', true);

        $otherCond = new \Zimbra\Account\Struct\EntrySearchFilterSingleCond('attr', CondOp::GE(), 'value', false);
        $otherConds = new \Zimbra\Account\Struct\EntrySearchFilterMultiCond(false, true, NULL, $otherCond);
        $cond = new \Zimbra\Account\Struct\EntrySearchFilterSingleCond('a', CondOp::EQ(), 'v', true);
        $conds = new \Zimbra\Account\Struct\EntrySearchFilterMultiCond(true, false, $otherConds, $cond);
        $filter = new \Zimbra\Account\Struct\EntrySearchFilterInfo($conds, $cond);

        $req = new \Zimbra\Account\Request\SearchCalendarResources(
            'locale', $cursor, 'name', $filter, false, 'sortBy', 100, 100, 'galAcctId', 'attrs'
        );
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame('locale', $req->locale());
        $this->assertSame($cursor, $req->cursor());
        $this->assertSame('name', $req->name());
        $this->assertSame($filter, $req->searchFilter());
        $this->assertFalse($req->quick());
        $this->assertSame('sortBy', $req->sortBy());
        $this->assertSame(100, $req->limit());
        $this->assertSame(100, $req->offset());
        $this->assertSame('galAcctId', $req->galAcctId());
        $this->assertSame('attrs', $req->attrs());

        $req->locale('locale')
            ->cursor($cursor)
            ->name('name')
            ->searchFilter($filter)
            ->quick(true)
            ->sortBy('sortBy')
            ->limit(10)
            ->offset(10)
            ->galAcctId('galAcctId')
            ->attrs('attrs');
        $this->assertSame('locale', $req->locale());
        $this->assertSame($cursor, $req->cursor());
        $this->assertSame('name', $req->name());
        $this->assertSame($filter, $req->searchFilter());
        $this->assertTrue($req->quick());
        $this->assertSame('sortBy', $req->sortBy());
        $this->assertSame(10, $req->limit());
        $this->assertSame(10, $req->offset());
        $this->assertSame('galAcctId', $req->galAcctId());
        $this->assertSame('attrs', $req->attrs());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<SearchCalendarResourcesRequest quick="true" sortBy="sortBy" limit="10" offset="10" galAcctId="galAcctId" attrs="attrs">'
                .'<locale>locale</locale>'
                .'<cursor id="id" sortVal="sortVal" endSortVal="endSortVal" includeOffset="true" />'
                .'<name>name</name>'
                .'<searchFilter>'
                    .'<conds not="true" or="false">'
                        .'<conds not="false" or="true">'
                            .'<cond attr="attr" op="ge" value="value" not="false" />'
                        .'</conds>'
                        .'<cond attr="a" op="eq" value="v" not="true" />'
                    .'</conds>'
                .'</searchFilter>'
            .'</SearchCalendarResourcesRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'SearchCalendarResourcesRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'name' => 'name',
                'locale' => 'locale',
                'quick' => true,
                'sortBy' => 'sortBy',
                'limit' => 10,
                'offset' => 10,
                'galAcctId' => 'galAcctId',
                'attrs' => 'attrs',
                'cursor' => array(
                    'id' => 'id',
                    'sortVal' => 'sortVal',
                    'endSortVal' => 'endSortVal',
                    'includeOffset' => true,
                ),
                'searchFilter' => array(
                    'conds' => array(
                        'not' => true,
                        'or' => false,
                        'conds' => array(
                            'not' => false,
                            'or' => true,
                            'cond' => array(
                                'attr' => 'attr',
                                'op' => 'ge',
                                'value' => 'value',
                                'not' => false,
                            ),
                        ),
                        'cond' => array(
                            'attr' => 'a',
                            'op' => 'eq',
                            'value' => 'v',
                            'not' => true,
                        ),
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testSearchGal()
    {
        $cursor = new \Zimbra\Struct\CursorInfo('id','sortVal', 'endSortVal', true);

        $otherCond = new \Zimbra\Account\Struct\EntrySearchFilterSingleCond('attr', CondOp::GE(), 'value', false);
        $otherConds = new \Zimbra\Account\Struct\EntrySearchFilterMultiCond(false, true, NULL, $otherCond);
        $cond = new \Zimbra\Account\Struct\EntrySearchFilterSingleCond('a', CondOp::EQ(), 'v', true);
        $conds = new \Zimbra\Account\Struct\EntrySearchFilterMultiCond(true, false, $otherConds, $cond);
        $filter = new \Zimbra\Account\Struct\EntrySearchFilterInfo($conds, $cond);

        $req = new \Zimbra\Account\Request\SearchGal(
            'locale', $cursor, $filter, 'ref', 'name', SearchType::ALL(),
            true, false, MemberOf::ALL(), true, 'galAcctId', false, SortBy::NONE(), 100, 100
        );
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame($cursor, $req->cursor());
        $this->assertSame($filter, $req->searchFilter());
        $this->assertSame('locale', $req->locale());
        $this->assertSame('ref', $req->ref());
        $this->assertSame('name', $req->name());
        $this->assertSame('all', $req->type()->value());
        $this->assertTrue($req->needExp());
        $this->assertFalse($req->needIsOwner());
        $this->assertSame('all', $req->needIsMember()->value());
        $this->assertTrue($req->needSMIMECerts());
        $this->assertSame('galAcctId', $req->galAcctId());
        $this->assertFalse($req->quick());
        $this->assertSame('none', $req->sortBy()->value());
        $this->assertSame(100, $req->limit());
        $this->assertSame(100, $req->offset());

        $req->locale('locale')
            ->cursor($cursor)
            ->searchFilter($filter)
            ->ref('ref')
            ->name('name')
            ->type(SearchType::ACCOUNT())
            ->needExp(true)
            ->needIsOwner(true)
            ->needIsMember(MemberOf::DIRECT_ONLY())
            ->needSMIMECerts(true)
            ->galAcctId('galAcctId')
            ->quick(true)
            ->sortBy(SortBy::DATE_ASC())
            ->limit(10)
            ->offset(10);
        $this->assertSame($cursor, $req->cursor());
        $this->assertSame($filter, $req->searchFilter());
        $this->assertSame('locale', $req->locale());
        $this->assertSame('ref', $req->ref());
        $this->assertSame('name', $req->name());
        $this->assertSame('account', $req->type()->value());
        $this->assertTrue($req->needExp());
        $this->assertTrue($req->needIsOwner());
        $this->assertSame('directOnly', $req->needIsMember()->value());
        $this->assertTrue($req->needSMIMECerts());
        $this->assertSame('galAcctId', $req->galAcctId());
        $this->assertTrue($req->quick());
        $this->assertSame('dateAsc', $req->sortBy()->value());
        $this->assertSame(10, $req->limit());
        $this->assertSame(10, $req->offset());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<SearchGalRequest ref="ref" name="name" type="account" needExp="true" needIsOwner="true" needIsMember="directOnly" needSMIMECerts="true" galAcctId="galAcctId" quick="true" sortBy="dateAsc" limit="10" offset="10">'
                .'<locale>locale</locale>'
                .'<cursor id="id" sortVal="sortVal" endSortVal="endSortVal" includeOffset="true" />'
                .'<searchFilter>'
                    .'<conds not="true" or="false">'
                        .'<conds not="false" or="true">'
                            .'<cond attr="attr" op="ge" value="value" not="false" />'
                        .'</conds>'
                        .'<cond attr="a" op="eq" value="v" not="true" />'
                    .'</conds>'
                .'</searchFilter>'
            .'</SearchGalRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'SearchGalRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'locale' => 'locale',
                'ref' => 'ref',
                'name' => 'name',
                'type' => 'account',
                'needExp' => true,
                'needIsOwner' => true,
                'needIsMember' => 'directOnly',
                'needSMIMECerts' => true,
                'galAcctId' => 'galAcctId',
                'quick' => true,
                'sortBy' => 'dateAsc',
                'limit' => 10,
                'offset' => 10,
                'cursor' => array(
                    'id' => 'id',
                    'sortVal' => 'sortVal',
                    'endSortVal' => 'endSortVal',
                    'includeOffset' => true,
                ),
                'searchFilter' => array(
                    'conds' => array(
                        'not' => true,
                        'or' => false,
                        'conds' => array(
                            'not' => false,
                            'or' => true,
                            'cond' => array(
                                'attr' => 'attr',
                                'op' => 'ge',
                                'value' => 'value',
                                'not' => false,
                            ),
                        ),
                        'cond' => array(
                            'attr' => 'a',
                            'op' => 'eq',
                            'value' => 'v',
                            'not' => true,
                        ),
                    ),
                ),
            ),
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testSubscribeDistributionList()
    {
        $dl = new \Zimbra\Account\Struct\DistributionListSelector(DLBy::NAME(), 'value');

        $req = new \Zimbra\Account\Request\SubscribeDistributionList(DLSubscribeOp::UNSUBSCRIBE(), $dl);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame('unsubscribe', $req->op()->value());
        $this->assertSame($dl, $req->dl());

        $req->op(DLSubscribeOp::SUBSCRIBE())
            ->dl($dl);
        $this->assertSame('subscribe', $req->op()->value());
        $this->assertSame($dl, $req->dl());

        $xml = '<?xml version="1.0"?>'."\n"
            .'<SubscribeDistributionListRequest op="subscribe">'
                .'<dl by="name">value</dl>'
            .'</SubscribeDistributionListRequest>';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'SubscribeDistributionListRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'op' => 'subscribe',
                'dl' => array(
                    'by' => 'name',
                    '_content' => 'value',
                ),
            )
        );
        $this->assertEquals($array, $req->toArray());
    }

    public function testSyncGal()
    {
        $req = new \Zimbra\Account\Request\SyncGal('token', 'galAcctId', false);
        $this->assertInstanceOf('Zimbra\Account\Request\Base', $req);
        $this->assertSame('token', $req->token());
        $this->assertSame('galAcctId', $req->galAcctId());
        $this->assertFalse($req->idOnly());

        $req->token('token ')
            ->galAcctId('galAcctId')
            ->idOnly(true);
        $this->assertSame('token', $req->token());
        $this->assertSame('galAcctId', $req->galAcctId());
        $this->assertTrue($req->idOnly());


        $xml = '<?xml version="1.0"?>'."\n"
            .'<SyncGalRequest token="token" galAcctId="galAcctId" idOnly="true" />';
        $this->assertXmlStringEqualsXmlString($xml, (string) $req);

        $array = array(
            'SyncGalRequest' => array(
                '_jsns' => 'urn:zimbraAccount',
                'token' => 'token',
                'galAcctId' => 'galAcctId',
                'idOnly' => true,
            )
        );
        $this->assertEquals($array, $req->toArray());
    }
}
