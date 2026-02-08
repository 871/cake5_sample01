<?php
declare(strict_types=1);

namespace App\Controller\Sample;

use App\Controller\AppController;

/**
 * MySqlTypeSamples Controller
 *
 * @property \App\Model\Table\Sample\MySqlTypeSamplesTable $MySqlTypeSamples
 */
class MySqlTypeSamplesController extends AppController
{

    public function initialize(): void
    {
        
        parent::initialize();

        $this->MySqlTypeSamples = $this->fetchTable(\App\Model\Table\Sample\MySqlTypeSamplesTable::class);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->MySqlTypeSamples->find();
        $mySqlTypeSamples = $this->paginate($query);

        $this->set(compact('mySqlTypeSamples'));
    }

    /**
     * View method
     *
     * @param string|null $id My Sql Type Sample id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $mySqlTypeSample = $this->MySqlTypeSamples->get($id, contain: []);
        $this->set(compact('mySqlTypeSample'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $mySqlTypeSample = $this->MySqlTypeSamples->newEmptyEntity();
        if ($this->request->is('post')) {
            $mySqlTypeSample = $this->MySqlTypeSamples->patchEntity($mySqlTypeSample, $this->request->getData());
            if ($this->MySqlTypeSamples->save($mySqlTypeSample)) {
                $this->Flash->success(__('The my sql type sample has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The my sql type sample could not be saved. Please, try again.'));
        }
        $this->set(compact('mySqlTypeSample'));
    }

    /**
     * Edit method
     *
     * @param string|null $id My Sql Type Sample id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $mySqlTypeSample = $this->MySqlTypeSamples->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mySqlTypeSample = $this->MySqlTypeSamples->patchEntity($mySqlTypeSample, $this->request->getData());
            if ($this->MySqlTypeSamples->save($mySqlTypeSample)) {
                $this->Flash->success(__('The my sql type sample has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The my sql type sample could not be saved. Please, try again.'));
        }
        $this->set(compact('mySqlTypeSample'));
    }

    /**
     * Delete method
     *
     * @param string|null $id My Sql Type Sample id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mySqlTypeSample = $this->MySqlTypeSamples->get($id);
        if ($this->MySqlTypeSamples->delete($mySqlTypeSample)) {
            $this->Flash->success(__('The my sql type sample has been deleted.'));
        } else {
            $this->Flash->error(__('The my sql type sample could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
