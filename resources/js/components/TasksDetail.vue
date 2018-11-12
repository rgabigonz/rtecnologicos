<template>
    <div class="container">
        <div class="row mt-5">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Actividades Tarea</h3>

                <div class="card-tools">
                    <router-link to="/tasks" class="btn btn-dark">
                        <i class="fas fa-hand-point-left fa-fw"></i>
                    </router-link>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th style="width: 93%">Descripcion</th>
                            <th style="width: 7%"></th>
                        </tr>
                        <tr v-for="taskD in taskDetails" :key="taskD.id">
                            <td>{{ taskD.description }}</td>
                            <td nowrap>
                                <a href="#" @click="editTaskD(taskD)">
                                    <i class="fa fa-edit blue"></i>
                                </a>
                                <a href="#" @click="deleteTaskDetail(taskD.id)">
                                    <i class="fa fa-trash red"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table">
                    <tbody>
                        <tr>
                            <td>
                                <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Nueva Actividad</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form class="form-horizontal" @submit.prevent="editMode ? updateTaskDetail() : createTaskDetail()">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <textarea v-model="form.description" type="text" name="description" placeholder="Actividad"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
                                            <has-error :form="form" field="description"></has-error>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-success">Guardar detalle</button>
                                    </div>
                                    <!-- /.card-footer -->
                                </form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                editMode: false,
                taskDetails: {},
                form: new Form({
                    id: '',
                    task_id: '',
                    description: ''                    
                }),
                taskId: 0
            }
        },
        methods: {
            loadTaskDetails() {
                axios.get("api/taskdetail/" + this.taskId).then(({ data }) => (this.taskDetails = data));
            },
            createTaskDetail() {
                this.$Progress.start();
                console.log(this.form.task_id);
                console.log(this.taskId);
                this.form.task_id = this.taskId;
                this.form.post('api/taskdetail')
                .then(() => {
                    Fire.$emit('AfterAction');
                    toast({
                        type: 'success',
                        title: 'Se creo la actividad correctamente!'
                    });
                    this.form.reset();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

                this.$Progress.finish();
            },
            editTaskD(taskD) {
                    this.editMode = true;
                    this.form.reset();
                    this.form.fill(taskD);
            },
            updateTaskDetail() {
                this.$Progress.start();
                
                this.form.put('api/taskdetail/'+this.form.id)
                .then(() => {
                    this.editMode = false;
                    this.form.reset();
                    Fire.$emit('AfterAction');
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
            deleteTaskDetail(id) {
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
                        this.form.delete('api/taskdetail/'+id)
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
            this.taskId = this.$route.params.taskId;
            this.loadTaskDetails();
            Fire.$on('AfterAction', () => {
                this.loadTaskDetails();
            });
        }
    }
</script>
