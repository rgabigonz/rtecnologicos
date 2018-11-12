<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Task;
use App\TaskDetail;
use DateTime;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sBuscar = $request->buscar;
        $sCriterio = $request->criterio;

        $user = auth('api')->user();

        if(empty($sBuscar)) {
            $tasks = Task::where('user_id', $user->id)
            ->orderBy('completed', 'asc')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
        } 
        else {
            if($sCriterio == 'created_at') {
                $tasks = Task::where('user_id', $user->id)
                ->whereDate($sCriterio, $sBuscar)
                ->orderBy('completed', 'asc')
                ->orderBy('updated_at', 'desc')
                ->paginate(15);
            }
            else {
                $tasks = Task::where('user_id', $user->id)
                ->where($sCriterio, 'like', '%' . $sBuscar . '%')
                ->orderBy('completed', 'asc')
                ->orderBy('updated_at', 'desc')
                ->paginate(15);
            }                
        }

        //return $tasks;
        return [
            'pagination' => [
                'total'         => $tasks->total(),
                'current_page'  => $tasks->currentPage(),
                'per_page'      => $tasks->perPage(),
                'last_page'     => $tasks->lastPage(),
                'from'          => $tasks->firstItem(),
                'to'            => $tasks->lastItem(),
            ],
            'tasks' => $tasks
        ];

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();

        $this->validate($request, [
            'name' => 'required|string|max:191'
        ], [
            'name.required' => 'El nombre es requerido',
        ]);

        return Task::create([
            'name' => $request['name'],
            'user_id' => $user->id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191'
        ], [
            'name.required' => 'El nombre es requerido',
        ]);

        $task->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
    }

    //Procesos extraordinarios de las tareas

    public function updateState($id)
    {
        $task = Task::findOrFail($id);
        $completed_at = new DateTime();

        if (!$task->completed) {
            $task->completed_at = $completed_at;
            $task->completed = true;
        }
        else {
            $task->completed_at = null;
            $task->completed = false;
        }

        $task->update();
        return ['message' => $task];
    }

    public function taskStatistics()
    {
        $user = auth('api')->user();

        $allTasks = Task::where('user_id', $user->id)
        ->count();

        $completedTasks = Task::where('user_id', $user->id)
        ->where('completed', true)
        ->count();

        $remainingTasks = $allTasks - $completedTasks;

        return [
            'allTasks'       => $allTasks,
            'completedTasks' => $completedTasks,
            'remainingTasks' => $remainingTasks
        ];
    }
    
    //Procesos extraordinarios de las tareas

    //Procesos detalles de las tareas

    public function taskDetail($id)
    {
        $taskDetail = Task::find($id)->details;
        return $taskDetail;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDetail(Request $request)
    {
        $this->validate($request, [
            'description' => 'required|string'
        ], [
            'description.required' => 'La descripcion es requerida',
        ]);

        return TaskDetail::create([
            'description' => $request['description'],
            'task_id' => $request['task_id'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDetail(Request $request, $id)
    {
        $taskD = TaskDetail::findOrFail($id);

        $this->validate($request, [
            'description' => 'required|string'
        ], [
            'description.required' => 'La descripcion es requerida',
        ]);

        $taskD->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyDelete($id)
    {
        $taskD = TaskDetail::findOrFail($id);
        $taskD->delete();
    }

    //Procesos detalles de las tareas

}
