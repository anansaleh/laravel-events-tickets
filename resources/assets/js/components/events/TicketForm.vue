<style scoped>
    .ticket-form .table-data{
        overflow-y: scroll;
        overflow-x: hidden;
        max-height: 450px;
    }
</style>
<template>
    <div class="ticket-form">
        <el-row :gutter="20">
            <el-col :span="24">
                <div class="card card-default"
                     v-loading="spinner"
                     element-loading-text="Loading data...">
                    <div class="card-header">Add Tickets to Events: <strong>{{event.name}}</strong> </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th scope="row">ID: </th>
                                <td>{{event.event_id}}</td>
                                <th scope="row">Date: </th>
                                <td colspan="3">{{event.event_date}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Current Tickets: </th>
                                <td>{{event.number_of_tickets}}</td>
                                <th scope="row">Current Active: </th>
                                <td>{{event.number_of_ok}}</td>
                                <th scope="row">Current Redeemed: </th>
                                <td>{{event.number_of_redeemed}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="4">
                                    <el-form ref="ticketForm"
                                             :inline="true"
                                             :model="ticketForm"
                                             :rules="validationsRules"
                                             class="demo-form-inline">
                                        <el-form-item label="No. Tickets">
                                            <el-input v-model="ticketForm.tickets" placeholder="Number of tickets to add"></el-input>
                                        </el-form-item>

                                        <el-form-item>
                                            <el-button type="primary" @click="addTicket" icon="el-icon-circle-plus">add Ticket</el-button>
                                        </el-form-item>

                                    </el-form>
                                </td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </el-col>
        </el-row>
        <el-divider></el-divider>
        <el-row :gutter="20" v-if="newTicketsList.length>0">
            <el-col :span="24">
                <div class="card card-default">
                    <div class="card-header">
                        <el-row :gutter="20">
                            <el-col :span="18">
                                <h4>New Tickets List</h4>
                            </el-col>
                            <el-col :span="6" class="text-right">
                                <download-csv
                                        :data="newTicketsList"
                                        :name="dataFile"
                                        :labels="labels"
                                        :fields="fields"
                                        v-on:export-finished="exported"
                                >
                                    <el-button type="primary" icon="el-icon-download"  size="small" >Download</el-button>
                                </download-csv>

                            </el-col>
                        </el-row>
                    </div>

                    <div class="card-body">
                        <div class="table-data">
                            <table class="table table-striped">
                                <thead>
                                <th v-for="(value, name) in labels">{{ value }}</th>
                                </thead>
                                <tbody>
                                <tr  v-for="item in newTicketsList">
                                    <td>{{item.index}}</td>
                                    <td>{{item.event_date}}</td>
                                    <td>{{item.event_name}}</td>
                                    <td>{{item.ticket_id}}</td>
                                    <td><i :class="getIcon(item.status)"></i></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        name: "ticket-form"
        , data(){
            let vm = this;
            let validateTickets = function (rule, value, callback){
                if (value === '') {
                    callback(new Error('Please input No. Tickets'));
                } else {
                    if (Number.isNaN( Number.parseFloat(value) )) {
                        callback(new Error('Please input number'));
                    } else if (parseFloat(value) <= 0) {
                        callback(new Error('Number must be greater than 0'));
                    }
                    else {
                        callback();
                    }
                }
            };
            return{
                spinner: false
                , event: {
                    event_id: 0
                    , name:''
                    , event_date: ''
                    , number_of_tickets:0
                    , number_of_ok:0
                    , number_of_redeemed:0
                }
                , newTicketsList:[]
                , ticketForm:{
                    tickets:0
                }
                , validationsRules: {
                    tickets: [
                        {validator: validateTickets, trigger: 'blur'}
                    ]
                }
                , labels: {
                    index: 'No'
                    , event_date: 'Event Date'
                    , event_name: 'Event Name'
                    , ticket_id: 'Ticket Code'
                    , status: 'Ticket Status'
                }
                , fields : ['index' ,'event_date', 'event_name', 'ticket_id', 'status']
                , isExported: false
            }
        }
        , props: {
            event_id:{
                required: true
            }
        }
        , computed: {
            dataFile: function(){
                return  'new_tickets_event_'+ this.event_id+ '.csv';
            }
        }
        , created() {
            this.loadData();
        }
        , methods:{
            loadData(){
                var self = this;
                self.spinner=true;
                axios({
                    method:'GET'
                    , url: '/api/events/' + self.$route.params.event_id
                })
                    .then(function(response) {
                        self.spinner = false;
                        self.event=response.data;

                    })
                    .catch(e => {
                        self.spinner = false;
                        self.$message({
                            type:'error'
                            , message:e.response.data.error.message
                        });
                    });
            }
            , getIcon(status){
                return (status == 'OK')? 'el-icon-success text-success' :  'el-icon-error text-danger';
            }
            , addTicket(){
                let self = this;
                this.$refs['ticketForm'].validate(function(valid){
                    if (valid) {
                        self.spinner=true;
                        axios({
                            method:'POST'
                            , url: '/api/events/'+ self.event_id + '/add-tickets'
                            , data: self.ticketForm
                        })
                            .then(function(response) {
                                //console.log(response);
                                self.spinner = false;
                                self.fillJsonData(response.data);
                                self.loadData();
                                self.$emit('successAddTickets');
                            })
                            .catch(e => {
                                self.spinner = false;
                                self.$message({
                                    type:'error'
                                    , message:e.response.data.error.message
                                });
                            });

                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            }
            , fillJsonData(data){
                let self =this;
                let myArray =[];
                let k = self.newTicketsList.length +1;
                data.forEach(function(el) {
                    let item={
                        index: k
                        , event_date: el.event_date
                        , event_name: el.event_name
                        , ticket_id: el.ticket_id
                        , status: (el.status)? 'OK' :  'Redeemed'
                    };
                    myArray.push(item);
                    k++;
                });
                this.newTicketsList = this.newTicketsList.concat(myArray);
            }
            , exported(event) {
                this.isExported = true
                let self= this;
                setTimeout(() => {
                    self.isExported = false
                    self.$message({
                        type: 'success'
                        , message:'Success download file'
                    });
                }, 3 * 1000)
            }
        }
    }
</script>

