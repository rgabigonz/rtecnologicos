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
                                <a href="#" @click="editModal(taskD)" v-show="!taskD.completed">
                                    <i class="fa fa-edit blue"></i>
                                </a>
                                <a href="#" @click="deleteTask(taskD.id)" v-show="!taskD.completed">
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
                                <form class="form-horizontal" @submit.prevent="createTaskDetail()">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <textarea v-model="form.description" type="text" name="description" placeholder="Actividad"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
                                            <has-error :form="form" field="description"></has-error>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary float-right">Agregar</button>
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
                
                this.form.task_id = this.taskId;
                this.form.post('api/taskdetail')
                .then(() => {
                    Fire.$emit('AfterAction');
                    toast({
                        type: 'success',
                        title: 'Se creo la actividad correctamente!'
                    });

                })
                .catch(() => {
                    this.$Progress.fail();
                });

                this.$Progress.finish();
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
