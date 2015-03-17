<?php
/**
 * "UserActivityListener" is a bootstrap component
 * It is a user event logger which watch ActiveRecord changes
 * and write who and when make changes in history table
 *
 * @author yiiBoy <muravyov.alexey@gmail.com>
 * @version 1.0 2015-03-17
 *
 */
namespace yiiBoy\activity;

use Yii;
use Closure;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\Expression;
use yii\db\ActiveRecord;

use yii\log\Logger;

class UserActivityListener implements BootstrapInterface
{
    /**
     * @var array of arrays [ className, eventName ]
     */
    public $watchClassNameEventName = [];
    /**
     * @var string set class name for save activity
     */
    public $activityClass = '\modules\template\models\TemplateChange';
    /**
     * @var string set attribute name for entity id
     */
    public $entityAttribute = 'entity_id';
    /**
     * @var string set attribute name for actibity type
     */
    public $activityAttribute = 'activity';
    public $createdAtAttribute = 'created_at';
    public $createdByAttribute = 'created_by';
    /**
     * @var mixed string or callback used for get createdAtValue by event
     */
    public $createdAtValue;
    /**
     * @var mixed string or callback used for get createdByValue by event
     */
    public $createdByValue;
    /**
     * @var mixed callback used for get activity name or id by event
     */
    public $getActivityIdByEvent;

    public function bootstrap($app)
    {
        Yii::trace('UserActivityListener:bootstrap');
        // if ($this->createdAtValue === null) {
        //     $this->createdAtValue = time(); // new Expression('NOW()');
        // }
        if ($this->createdByValue === null) {
            $this->createdByValue = Yii::$app->user->id;
        }

        foreach ($this->watchClassNameEventName as $classNameEventName) {
            list($className,$eventName) = $classNameEventName;
            Event::on($className, $eventName, [$this, 'handler'], $this);
        }
        return $app;
    }

    /**
     * @inheritdoc
     */
    public static function handler($event)
    {
        $listener = $event->data;

        $change = new $listener->activityClass;
        $change->{$listener->entityAttribute} = $event->sender->id;
        if (is_callable($listener->getActivityIdByEvent)) {
            $change->{$listener->activityAttribute} = call_user_func($listener->getActivityIdByEvent, $event);
        }
        if ($listener->createdAtAttribute) {
            $change->{$listener->createdAtAttribute} = $listener->getCreatedAtValue($event);
        }
        if ($listener->createdByAttribute) {
            $change->{$listener->createdByAttribute} = $listener->getCreatedByValue($event);
        }

        $change->save();
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAtValue($event)
    {
        if ($this->createdAtValue instanceof Expression) {
            return $this->createdAtValue;
        } else {
            return $this->createdAtValue !== null ? call_user_func($this->createdAtValue, $event) : time();
        }
    }

    /**
     * @inheritdoc
     */
    public function getCreatedByValue($event)
    {
        return $this->createdByValue instanceof Closure ? call_user_func($this->createdByValue, $event) : $this->createdByValue;
    }
}
