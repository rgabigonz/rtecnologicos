<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="card card-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header text-black"> <!-- style="background-image:url('./img/profile.png')"-->
                        <h3 class="widget-user-username">{{ form.name }}</h3>
                        <h5 class="widget-user-desc">Analista de Recursos Tecnologicos</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
                    </div>
                    <div class="card-footer">
                        <div class="row">
                          <div class="col-sm-4 border-right">
                              <div class="description-block">
                                <h5 class="description-header">{{ allTasks }}</h5>
                                <span class="description-text">TAREAS TOTALES</span>
                              </div>
                              <!-- /.description-block -->
                          </div>
                          <!-- /.col -->
                          <div class="col-sm-4 border-right">
                              <div class="description-block">
                                <h5 class="description-header">{{ remainingTasks }}</h5>
                                <span class="description-text">TAREAS RESTANTES</span>
                              </div>
                              <!-- /.description-block -->
                          </div>
                          <!-- /.col -->
                          <div class="col-sm-4">
                              <div class="description-block">
                                <h5 class="description-header">{{ completedTasks }}</h5>
                                <span class="description-text">TAREAS COMPLETAS</span>
                              </div>
                              <!-- /.description-block -->
                          </div>
                          <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>                
            </div>
            <div class="col-md-12 mt-3">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Actividad</a></li>
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Datos</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">

                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="settings">
                    <form class="form-horizontal">
                      <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Nombre</label>

                        <div class="col-sm-12">
                            <input v-model="form.name" type="text" name="name" placeholder="Nombre"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">Email</label>

                        <div class="col-sm-12">
                            <input v-model="form.email" type="email" name="email" placeholder="Email"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="bio" class="col-sm-2 control-label">Experiencia</label>

                        <div class="col-sm-12">
                            <textarea v-model="form.bio" type="text" name="bio" id="bio" placeholder="Experiencia"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                            <has-error :form="form" field="bio"></has-error>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="photo" class="col-sm-2 control-label">Foto de Perfil</label>

                        <div class="col-sm-12">
                          <input type="file" @change="updateProfile" name="photo" class="form-input" accept=".jpg,.jpeg,.png">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="password" class="col-sm-2 control-label">Contraseña</label>

                        <div class="col-sm-12">
                            <input v-model="form.password" type="password" name="password" id="password" placeholder="Contraseña"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                            <has-error :form="form" field="password"></has-error>
                        </div>
                      </div>

                      <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <button @click.prevent="updateInfo" type="submit" class="btn btn-success">Actualizar</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>            
        </div>
    </div>
</template>

<style>
    .widget-user-header {
        background-position: center center;
        background-size: cover;
        height: 250px;
    }
</style>

<script>
    export default {
        data() {
          return {
            form: new Form({
                id: '',
                name: '',
                email: '',
                password: '',
                type: '',
                bio: '',                    
                photo: ''                
            }),
            tmpPhoto: '',
            allTasks: 0,
            completedTasks: 0,
            remainingTasks: 0
          }
        },
        methods: {
          loadProfile() {
            axios.get("api/profile").then(({ data }) => (this.form.fill(data)));
            axios.get("api/taskstat")
            .then(( { data }) => {
              this.allTasks = data.allTasks;
              this.completedTasks = data.completedTasks;
              this.remainingTasks = data.remainingTasks;
            });
          },
          getProfilePhoto() {
            return "img/profile/" + this.form.photo;
          },
          updateProfile(e) {
            let file = e.target.files[0];
            let reader = new FileReader();

            if(file['size'] < 2111775) { //Menor a 2 MB
              reader.onloadend = (file) => {
                this.tmpPhoto = reader.result;
              }
              reader.readAsDataURL(file);
            } else {
              swal({
                type: 'error',
                title: 'Hubo un problema...',
                text: 'El archivo de imagen de perfil no puede tener mas de 2 MB',
              })
              e.target.value = null;
            }
          },
          updateInfo() {
            this.$Progress.start();
            
            this.form.photo = this.tmpPhoto;

            this.form.put('api/profile/')
            .then(() => {
                this.$Progress.finish();
                toast({
                    type: 'success',
                    title: 'Se actualizaron los datos correctamente!'
                });
                this.loadProfile();
            })
            .catch(() => {
                this.$Progress.fail();
            });            
          }
        },
        mounted() {
          console.log('Component mounted.')
        },
        created(){
          this.loadProfile();
        }
    }
</script>
