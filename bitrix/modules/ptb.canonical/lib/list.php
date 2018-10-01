<?
// ################################################
// Company: NicLab
// Site: https://www.psdtobitrix.ru
// Copyright (c) 2013-2017 NicLab
// ################################################


namespace Ptb\Canonical;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type\DateTime;
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class ListTable
 *
 * @author Nic-lab n.revin
 */
class ListTable extends Entity\DataManager
{

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'ptb_canonical_list';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_ID')
            )),
            'ACTIVE' => new Entity\BooleanField('ACTIVE', array(
                'values' => array(
                    'N',
                    'Y'
                ),
                'default_value' => 'Y',
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_ACTIVE')
            )),
            'DATE_CREATE' => new Entity\DatetimeField('DATE_CREATE', array(
                'default_value' => new DateTime(),
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_DATE_CREATE')
            )),
            'CREATED_BY' => new Entity\IntegerField('CREATED_BY', array(
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_CREATED_BY')
            )),
            'TIMESTAMP_X' => new Entity\DatetimeField('TIMESTAMP_X', array(
                'default_value' => new DateTime(),
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_TIMESTAMP_X')
            )),
            'MODIFIED_BY' => new Entity\IntegerField('MODIFIED_BY', array(
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_MODIFIED_BY')
            )),
            'SITE_ID' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(
                    __CLASS__,
                    'validateSiteId'
                ),
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_SITE_ID')
            ),
            'PAGE' => new Entity\StringField('PAGE', array(
                'required' => true,
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_PAGE')
            )),
            'CANONICAL' => new Entity\StringField('CANONICAL', array(
                'required' => true,
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_CANONICAL')
            )),
            'USE_REGEXP' => new Entity\BooleanField('USE_REGEXP', array(
                'values' => array(
                    'N',
                    'Y'
                ),
                'default_value' => 'N',
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_USE_REGEXP')
            )),
            'SORT' => new Entity\IntegerField('SORT', array(
                'default_value' => 500,
                'title' => Loc::getMessage('PTB_CANONICAL_LIB_CANONICAL_SORT')
            )),
            'MODIFIED_BY_USER' => new Entity\ReferenceField('MODIFIED_BY_USER', 'Bitrix\Main\User', array(
                '=this.MODIFIED_BY' => 'ref.ID'
            ), array(
                'join_type' => 'LEFT'
            )),
            'CREATED_BY_USER' => new Entity\ReferenceField('CREATED_BY_USER', 'Bitrix\Main\User', array(
                '=this.CREATED_BY' => 'ref.ID'
            ), array(
                'join_type' => 'LEFT'
            )),
            'SITE' => array(
                'data_type' => 'Bitrix\Main\Site',
                'reference' => array(
                    '=this.SITE_ID' => 'ref.LID'
                )
            )
        );
    }

    /**
     * Returns validators for SITE_ID field.
     *
     * @return array
     */
    public static function validateSiteId()
    {
        return array(
            new Entity\Validator\Length(null, 2)
        );
    }

    public static function onBeforeAdd(Entity\Event $event)
    {
        global $USER;
        $result = new Entity\EventResult();

        $result->modifyFields(array(
            'MODIFIED_BY' => $USER->GetID(),
            'CREATED_BY' => $USER->GetID()
        ));

        return $result;
    }

    public static function onAfterAdd(Entity\Event $event)
    {
        $data = $event->getParameter("fields");
        $id = (array)$event->getParameter("id");
        $id = reset($id);

        self::addToEventLog('CANONICAL_ADD', $id, $data);
    }

    public static function onBeforeUpdate(Entity\Event $event)
    {
        global $USER;
        $result = new Entity\EventResult();

        $result->modifyFields(array(
            'MODIFIED_BY' => $USER->GetID()
        ));

        return $result;
    }

    public static function onAfterUpdate(Entity\Event $event)
    {
        $data = $event->getParameter("fields");
        $id = (array)$event->getParameter("id");
        $id = reset($id);

        self::addToEventLog('CANONICAL_UPDATE', $id, $data);
    }

    public static function onBeforeDelete(Entity\Event $event)
    {
        $id = (array)$event->getParameter("id");
        $id = reset($id);
        $data = self::getById($id)->fetch();
        self::addToEventLog('CANONICAL_DELETE', $id, $data);
    }

    private static function addToEventLog($type, $id, $data) {
        \CEventLog::Log(
            "PTB_CANONICAL",
            $type,
            "ptb.canonical",
            $id,
            serialize((array)$data)
        );
    }
}
?>