<?php
declare(strict_types=1);

namespace TestApp\Controller\Admin;

use TestApp\Controller\AppController;

/**
 * Articles Controller
 *
 * @property \Cake\ORM\Table $Articles
 */
class ArticlesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Articles->find();
        $articles = $this->paginate($query);

        $this->set(compact('articles'));
    }

    /**
     * Close method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function close($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $article = $this->Articles->get($id);

        return $this->redirect(['action' => 'view', $article->id]);
    }
}
