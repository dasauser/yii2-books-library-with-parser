<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $title
 * @property string $isbn
 * @property int|null $pageCount
 * @property string|null $publishedDate
 * @property string|null $thumbnailUrl
 * @property string|null $thumbnailImage
 * @property string|null $shortDescription
 * @property string|null $longDescription
 * @property string|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Author[] $authors
 * @property Category[] $categories
 */
class Book extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public array $categoriesList = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'books';
    }

    public function scenarios()
    {
        $attrs = [
            'title',
            'isbn',
            'pageCount',
            'publishedDate',
            'thumbnailUrl',
            'thumbnailImage',
            'shortDescription',
            'longDescription',
            'status',
            'created_at',
            'updated_at',
            'categoriesList'
        ];
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => $attrs,
            self::SCENARIO_UPDATE => $attrs,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'isbn'], 'required'],
            [['pageCount'], 'integer'],
            [['publishedDate', 'created_at', 'updated_at'], 'safe'],
            [['shortDescription', 'longDescription'], 'string'],
            [['title', 'thumbnailUrl', 'thumbnailImage', 'status'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 20],
            [['title'], 'unique'],
            [['isbn'], 'unique'],
            [['categoriesList'], 'each', 'rule' => ['integer']],
            [['categoriesList'], 'each', 'rule' => ['exist', 'targetClass' => Category::class, 'targetAttribute' => ['categoriesList' => 'id']]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'isbn' => 'Isbn',
            'pageCount' => 'Page Count',
            'publishedDate' => 'Published Date',
            'thumbnailUrl' => 'Thumbnail Url',
            'thumbnailImage' => 'Thumbnail Image',
            'shortDescription' => 'Short Description',
            'longDescription' => 'Long Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    /**
     * Gets query for [[Authors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])->viaTable('book_author', ['book_id' => 'id']);
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])->viaTable('book_category', ['book_id' => 'id']);
    }
}
