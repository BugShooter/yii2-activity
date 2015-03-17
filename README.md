# Yii2 logger of user activity

## How to use

### 1 Create table and model Activity with fiels

```sql
entity_id INT
activity_id INT
created_at TIMESTAMP
created_by INT
```

### 2 Create getActivityIdByEvent function if you want save activity_id

### 3 If you want to watch Model1 and Model2 events
###Append UserActivityListener to boopstrap config section like this

```php
'bootstrap' => array (
    [
        'class' => '\yiiBoy\activity\UserActivityListener',
        'watchClassNameEventName' => [
            ['\namespace\Model1', ActiveRecord::EVENT_AFTER_INSERT],
            ['\namespace\Model1', ActiveRecord::EVENT_AFTER_UPDATE],
            ['\namespace\Model1', ActiveRecord::EVENT_AFTER_DELETE],
            ['\namespace\Model2', ActiveRecord::EVENT_AFTER_INSERT],
            ['\namespace\Model2', ActiveRecord::EVENT_AFTER_UPDATE],
            ['\namespace\Model2', ActiveRecord::EVENT_AFTER_DELETE],
        ],
        'activityClass' => '\namespace\Activity',
        'entityAttribute' => 'entity_id',
        'activityAttribute' => 'activity_id',
        'createdAtAttribute' => 'created_at',
        'createdAtValue' => new Expression('NOW()'),
        'createdByAttribute' => 'created_by',
        'getActivityIdByEvent' => ['\namespace\Activity','getActivityIdByEvent'],
    ],
),
```
