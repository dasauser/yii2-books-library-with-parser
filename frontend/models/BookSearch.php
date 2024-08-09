<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Book;
use yii\helpers\VarDumper;

/**
 * BookSearch represents the model behind the search form of `common\models\Book`.
 */
class BookSearch extends Book
{
    public ?int $categoryId = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pageCount'], 'integer'],
            [['title', 'isbn', 'publishedDate', 'thumbnailUrl', 'thumbnailImage', 'shortDescription', 'longDescription', 'status', 'created_at', 'updated_at'], 'safe'],
            ['categoryId', 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Book::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if ($this->categoryId !== null) {
            $query->innerJoin('book_category', 'book_id = id AND category_id = '.$this->categoryId);
//        echo VarDumper::export($query->createCommand()->getRawSql());exit();
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'pageCount' => $this->pageCount,
            'publishedDate' => $this->publishedDate,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'isbn', $this->isbn])
            ->andFilterWhere(['like', 'thumbnailUrl', $this->thumbnailUrl])
            ->andFilterWhere(['like', 'thumbnailImage', $this->thumbnailImage])
            ->andFilterWhere(['like', 'shortDescription', $this->shortDescription])
            ->andFilterWhere(['like', 'longDescription', $this->longDescription])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
