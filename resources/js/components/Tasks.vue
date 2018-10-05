<template>
    <div class="container">
        <div class="row mt-5">
          <div class="col-md-12">
            <div class="card border-success mb-3">
                <div class="card-header bg-transparent border-light">Busqueda
                    <div class="card-tools">
                        <button class="btn btn-secondary" @click="loadTasks(1, sBuscar, sCriterio)"><i class="fas fa-search fa-fw"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col col-md-4">
                            <select class="form-control" v-model="sCriterio">
                                <option value="name">Tarea</option>
                                <option value="created_at">Fecha</option>
                            </select>
                        </div>
                        <div class="col col-md-8">
                            <input v-model="sBuscar" @keyup.enter="loadTasks(1, sBuscar, sCriterio)" type="text" class="form-control" placeholder="Dato a buscar (Fecha YYYY-MM-DD)">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-info mb-3">
              <div class="card-header border-light">
                <h3 class="card-title">Lista de Tareas</h3>
                <div class="card-tools">
                    <button class="btn btn-success" @click="newModal()"><i class="fas fa-plus fa-fw"></i></button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Tarea</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Terminada</th>
                            <th></th>
                        </tr>
                        <tr v-for="task in tasks" :key="task.id">
                            <td>{{ task.name }}</td>
                            <td>{{ task.created_at | formatDate }}</td>
                            <td>{{ task.completed | formatState }}</td>
                            <td>{{ task.completed_at | formatDate }}</td>
                            <td>
                                <a href="#" @click="editModal(task)" v-show="!task.completed">
                                    <i class="fa fa-edit blue"></i>
                                </a>
                                <a href="#" @click="deleteTask(task.id)" v-show="!task.completed">
                                    <i class="fa fa-trash red"></i>
                                </a>
                                <router-link :to="{ name: 'taskdetails', params: { taskId: task.id } }">
                                    <i class="fa fa-table indigo"></i>
                                </router-link>
                                <a href="#" @click="stateTask(task.id)">
                                    <i v-if="task.completed" class="fa fa-history red"></i>
                                    <i v-else class="fa fa-check green"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                  <li class="page-item" v-if="pagination.current_page > 1">
                      <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1, sBuscar, sCriterio)">«</a>
                  </li>
                  <li class="page-item" v-for="page in pageNumber" :key="page" :class="[page == isActived ? 'active': '']">
                      <a class="page-link" href="#" @click.prevent="cambiarPagina(page, sBuscar, sCriterio)" v-text="page"></a>
                  </li>
                  <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                      <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1, sBuscar, sCriterio)">»</a>
                  </li>
                </ul>
              </div>
              <!-- /.card-footer -->           
            </div>
            <!-- /.card -->
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editMode" class="modal-title" id="addNewLabel">Agregar Tarea</h5>
                        <h5 v-show="editMode" class="modal-title" id="addNewLabel">Editar Tarea</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode ? updateTask() : createTask()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name" placeholder="Nombre"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            <button v-show="editMode" type="submit" class="btn btn-success">Actualizar</button>
                            <button v-show="!editMode" type="submit" class="btn btn-primary">Crear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                editMode: false,
                tasks: {},
                form: new Form({
                    id: '',
                    name: ''                    
                }),
                pagination: {
                    'total': 0,
                    'current_page': 0,
                    'per_page': 0,
                    'last_page': 0,
                    'from': 0,
                    'to': 0
                },
                offset: 3,
                sCriterio: 'name',
                sBuscar: ''
            }
        },
        computed: {
            isActived: function() {
                return this.pagination.current_page;
            },
            pageNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }

                var from = this.pagination.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2);
                if(to >= this.pagination.last_page) {
                    to = this.pagination.last_page;
                }

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }

                return pagesArray;
            }
        },
        methods: {
            cambiarPagina(page, buscar, criterio){
                //let me = this;
                this.pagination.current_page = page;
                this.loadTasks(page, buscar, criterio);
            },
            newModal() {
                this.editMode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            editModal(task) {
                this.editMode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(task);
            },
            closeModal() {
                this.form.reset();
                $('#addNew').modal('hide');
            },
            loadTasks(page, buscar, criterio) {
                let me = this;                
                //axios.get("api/task?page=" + page).then(({ data }) => (this.tasks = data.data));
                var url = 'api/task?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
                axios.get(url).then(function (data) {
                    var response = data.data;
                    me.tasks = response.tasks.data;
                    me.pagination = response.pagination;
                });
            },
            searchTasks () {
                
            },
            updateTask() {
                this.$Progress.start();
                
                this.form.put('api/task/'+this.form.id)
                .then(() => {
                    Fire.$emit('AfterAction');
                    this.closeModal();
                    toast({
                        type: 'success',
                        title: 'Se actualizaron los datos correctamente!'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
            stateTask(taskId) {
                swal({
                    title: 'Ud. esta por cambiar el estado de la tarea, desea continuar?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Cambiar estado',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        this.form.put('api/state/'+taskId)
                        .then(() => {
                            swal(
                                'Cambiado!',
                                'El estado ha sido cambiado.',
                                'success'
                            );
                            Fire.$emit('AfterAction');
                        })
                    }
                })
                .catch(() => {
                    swal('Fallo!', 'Hubo un error al procesar la transaccion.', 'warning');                    
                });
            },
            createTask() {
                this.$Progress.start();
                
                this.form.post('api/task')
                .then(() => {
                    Fire.$emit('AfterAction');
                    this.closeModal();
                    toast({
                        type: 'success',
                        title: 'Se creo la tarea correctamente!'
                    });

                })
                .catch(() => {
                    this.$Progress.fail();
                });

                this.$Progress.finish();
            },
            deleteTask(id) {
                swal({
                    title: 'Esta seguro?',
                    text: "No se puede volver atras!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, eliminar registro',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        this.form.delete('api/task/'+id)
                        .then(() => {
                            swal(
                                'Borrado!',
                                'El registro ah sido eliminado.',
                                'success'
                            );
                            Fire.$emit('AfterAction');
                        })
                    }
                })
                .catch(() => {
                    swal('Fallo!', 'Hubo un error al procesar la transaccion.', 'warning');                    
                });
            }
        },
        created() {
            this.loadTasks(1, this.sBuscar, this.sCriterio);
            Fire.$on('AfterAction', () => {
                this.loadTasks(1, this.sBuscar, this.sCriterio);
            });
        }
    }
</script>
