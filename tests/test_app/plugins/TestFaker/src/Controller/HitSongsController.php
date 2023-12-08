<?php
declare(strict_types=1);

namespace TestFaker\Controller;

/**
 * HitSongs Controller
 *
 */
class HitSongsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->HitSongs->find();
        $hitSongs = $this->paginate($query);

        $this->set(compact('hitSongs'));
    }

    /**
     * View method
     *
     * @param string|null $id Hit Song id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function preview($id = null)
    {
        $hitSong = $this->HitSongs->get($id, contain: []);
        $this->set(compact('hitSong'));
    }
}
