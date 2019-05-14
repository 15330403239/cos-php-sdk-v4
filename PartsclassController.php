<?php

namespace backend\controllers;

use backend\models\CommonModel;
use Yii;
use common\models\PartsClass;
use backend\models\PartsclassSerach;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PartsclassController implements the CRUD actions for Partsclass model.
 */
class PartsclassController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Partsclass models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PartsclassSerach();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Partsclass model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Partsclass model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PartsClass();
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (empty($data['PartsClass']['class'])) {
                $data['PartsClass']['type'] = 1;
                $data['PartsClass']['class'] = 0;
            } else {
                $data['PartsClass']['type'] = 2;
            }
            $model->load($data);
            if ($model->validate($data) && $model->save()) {
                CommonModel::adminLog('/parts_class/create',CommonModel::logCode('1',"/parts_class/create",'配件分类添加'));
                return $this->redirect(['index', 'id' => $model->cid]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Partsclass model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            CommonModel::adminLog('/parts_class/update',CommonModel::logCode('1',"/parts_class/update",'配件分类修改'));
            return $this->redirect(['index', 'id' => $model->cid]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Partsclass model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
//        $this->findModel($id)->update(['status' => 20]);
        $query = PartsClass::find()->where(['class' => $id, 'status' => 10])->count();
        if ($query) {
            return $this->render('/site/errors',['path' => '该分类下还有子分类，不能删除！']);
        }
        $model = Yii::$app->db->createCommand()->update('parts_class', ['status' => 20], 'cid =' . $id)->execute();
        if ($model) {
            CommonModel::adminLog('/parts_class/delete',CommonModel::logCode('1',"/parts_class/delete",'配件分类删除'));
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Partsclass model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Partsclass the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PartsClass::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
