<template>
    <div class="container">
        <el-row :gutter="20">
            <el-col :span="24">
                <div class="card card-default">
                    <div class="card-header">Home page</div>

                    <div class="card-body">
                        <h2>Welcome Events home page</h2>
                        <p>This is home page for login user</p>
                    </div>
                </div>
            </el-col>


        </el-row>
        <el-divider></el-divider>
        <el-row :gutter="20">
            <el-col :span="24">
                <div class="card card-default">
                    <div class="card-header">Check Ticket</div>

                    <div class="card-body"
                         v-loading="spinner"
                         element-loading-text="Check Ticket...">

                        <el-form ref="ticketForm"
                                 :model="ticketForm"
                                 :rules="validationsRules"
                                 class="demo-form-inline">
                            <el-row>
                                <el-col :span="2">
                                    <el-form-item label="Ticket Code:"></el-form-item>
                                </el-col>
                                <el-col :span="16">
                                    <el-form-item >
                                        <el-input v-model="ticketForm.ticket_id" placeholder="Ticket Code"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="6">
                                    <el-form-item>
                                        <el-button type="primary" @click="checkTicket" icon="el-icon-search">Check</el-button>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </el-form>
                        <div class="alert alert-success  alert-dismissible" v-if="ticketOK">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Success!</strong> Ticket is OK.
                        </div>
                        <div class="alert alert-warning  alert-dismissible" v-if="ticketGone">
                            <a href="#" @click="close()" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Warning!</strong> Ticket is gone.
                        </div>
                        <div class="alert alert-danger  alert-dismissible" v-if="ticketNotFound">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Error!</strong> No ticket found with this number.
                        </div>
                        <table class="table table-striped" v-if="ticketObj">
                            <tbody>
                            <tr>
                                <th>Event Date</th>
                                <td>{{ticketObj.event_date}}</td>
                            </tr>
                            <tr>
                                <th>Event Name</th>
                                <td>{{ticketObj.event_name}}</td>
                            </tr>
                            <tr>
                                <th>Ticket Code</th>
                                <td>{{ticketObj.ticket_id}}</td>
                            </tr>
                            <tr>
                                <th>Ticket Status</th>
                                <td><i :class="getIcon(ticketObj.status)"></i></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </el-col>
        </el-row>

    </div>
</template>

<script>
    export default {
        name: "home"
        , data(){
            return {
                spinner: false
                , ticketForm:{
                    ticket_id: ''
                }
                , ticketOK:false
                , ticketGone:false
                , ticketNotFound:false
                , validationsRules: {
                    ticket_id:[
                        { required: true, message: 'Please enter ticket number', trigger: 'blur' }
                    ]
                }
                , ticketObj:null
            }
        }
        , methods:{
            checkTicket(){
                var self = this;
                self.ticketOK=false;
                self.ticketGone=false;
                self.ticketNotFound=false;
                self.ticketObj=null;
                this.$refs['ticketForm'].validate(function(valid) {
                    if (valid) {
                        self.spinner = true;
                        axios({
                            method: 'GET'
                            , url: '/api/redeem/' + self.ticketForm.ticket_id
                        })
                            .then(function (response) {
                                // console.log('--- Response --');
                                // console.log(response);
                                self.spinner = false;
                                self.ticketOK = true;
                                self.ticketObj=response.data;

                            })
                            .catch(e => {
                                console.log('==============');
                                //console.log(e.response);

                                self.spinner = false;
                                if(e.response.status == 410){
                                    self.ticketObj= e.response.data;
                                    self.ticketGone=true;
                                }else{
                                    self.ticketObj=null;
                                    self.ticketNotFound=true;

                                }
                                // self.$message({
                                //     type: 'error'
                                //     , message: e.response.data.error.message
                                // });
                            });
                    } else {
                        self.$message({
                            type: 'error'
                            , message: "Pleas enter ticket number"
                        });
                        return false;
                    }
                });
            }
            , getIcon(status){
                return (status)? 'el-icon-success text-success' :  'el-icon-error text-danger';
            }
            , close(){
                this.ticketObj=null;
            }
        }
    }
</script>

<style scoped>

</style>