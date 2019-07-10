<template>
    <div class="tickets-list container">
<!--        <router-link to="/events">Back to Events list</router-link>-->

        <div class="card card-default" v-loading="spinnerEvents" element-loading-text="Loading....">
            <div class="card-header">
                <el-page-header @back="goBack" content="Event Tickets">
                </el-page-header>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th scope="row">ID: </th>
                            <td colspan="3">{{event.event_id}}</td>
                        </tr>
                        <tr>
                            <th scope="row">Name: </th>
                            <td colspan="3">{{event.name}}</td>
                        </tr>
                        <tr>
                            <th scope="row">Date: </th>
                            <td colspan="3">{{event.event_date}}</td>
                        </tr>
                        <tr>
                            <th scope="row">Tickets: </th>
                            <td>{{event.number_of_tickets}}</td>
                            <td><a title="Download CSV File" href="#" @click.prevent="download('all')"><i class="el-icon-download"></i>CSV</a></td>
                            <td class="text-right"><el-button @click="loadAddTicketForm()" type="primary" icon="el-icon-circle-plus"  size="small" >Add New Tickets</el-button></td>
                        </tr>
                        <tr>
                            <th scope="row">Active Tickets: </th>
                            <td>{{event.number_of_ok}}</td>
                            <td colspan="2"><a title="Download CSV File" href="#" @click.prevent="download('ok')"><i class="el-icon-download"></i>CSV</a></td>
                        </tr>
                        <tr>
                            <th scope="row">Redeemed Tickets: </th>
                            <td>{{event.number_of_redeemed}}</td>
                            <td colspan="2"><a title="Download CSV File" href="#" @click.prevent="download('redeemed')"><i class="el-icon-download"></i>CSV</a></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <el-divider></el-divider>
        <div class="card card-default">
            <div class="card-header">
                <el-row :gutter="20">
                    <el-col :span="18">
                        <h4>Tickets List</h4>
                    </el-col>
                    <el-col :span="6" class="text-right">
                        <el-button @click="refresh()" type="primary" icon="el-icon-refresh"  size="small" >Refresh</el-button>
                    </el-col>

                </el-row>
            </div>
            <div class="card-body">
                <el-divider>Search</el-divider>
                <div class="card card-default">
                    <div class="card-body">
                        <el-row :gutter="20">
                            <el-col :span="12">
                                <el-row>
                                    <el-col :span="24"><h5 class="sub-title">Filter by Status</h5></el-col>
                                </el-row>
                                <el-row :gutter="20">
                                    <el-col :span="24">
                                        <el-radio-group v-model="filter_status" size="small">
                                            <el-radio-button label="">All</el-radio-button>
                                            <el-radio-button label="1">Active</el-radio-button>
                                            <el-radio-button label="0">Redeemed</el-radio-button>
                                        </el-radio-group>
                                    </el-col>
                                </el-row>

                            </el-col>
                            <el-col :span="12">
                                <el-row>
                                    <el-col :span="24"><h5 class="sub-title">Search Ticket Code</h5></el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="20">
                                        <el-input v-model="filter_ticket_id" placeholder="Search ticket"></el-input>
                                    </el-col>
                                    <el-col :span="4">
                                        <el-button @click="search()" type="primary" icon="el-icon-search"></el-button>
                                    </el-col>
                                </el-row>

                            </el-col>
                        </el-row>
                    </div>
                </div>
                <el-divider>List</el-divider>
                <el-row :gutter="20">
                    <vuetable ref="vuetable"
                              v-loading="spinner"
                              element-loading-text="Loading data from Server..."
                              :api-url="api_url"
                              :append-params="appendParams"

                              pagination-path="meta"
                              :fields="ticket_fields"
                              :css="css"
                              :sort-order="sortOrder"
                              :per-page="10"
                              @vuetable:pagination-data="onPaginationData"
                              @vuetable:loading="vuetableLoading"
                              @vuetable:load-success="vuetableLoadSuccess"
                    >
                        <template slot="status" slot-scope="props">
                            <el-tooltip class="item" effect="light" :content="getToolTip(props.rowData.status)" placement="top">
                                <a href="#" @click.prevent="updateStatus(props.rowData)"><i :class="getIcon(props.rowData.status)"></i></a>
                            </el-tooltip>

                        </template>
                    </vuetable>
                    <div class="vuetable-footer">
                        <div class="justify-content-end">
                            <vuetable-pagination-info ref="paginationInfo"></vuetable-pagination-info>
                        </div>

                        <vuetable-pagination-bootstrap ref="pagination"
                                                       @vuetable-pagination:change-page="onChangePage"
                        ></vuetable-pagination-bootstrap>
                    </div>
                </el-row>

            </div>
        </div>
        <el-dialog top="5vh" title="New Tickets" width="60%"
                   :visible.sync="showTicketForm"
                   :close-on-click-modal="false" append-to-body>
            <ticket-form v-if="showTicketForm"
                        :event_id="event.event_id"
                        @successAddTickets="onAddTickets"
            ></ticket-form>
        </el-dialog>
        <el-dialog  top="5vh" title="Download Tickets" width="60%"
                   :visible.sync="showDownloadForm"
                   :close-on-click-modal="false" append-to-body>
            <download-form v-if="showDownloadForm"
                            :event_id="route_event"
                            :type="download_type"
            ></download-form>
        </el-dialog>
    </div>
</template>

<script>
    import axios from 'axios';
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePaginationBootstrap from '../common/VuetablePaginationBootstrap'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
    import BootstrapStyle from '../common/bootstrap-css.js'
    import ticketForm from '../events/TicketForm.vue'
    import downloadForm from './DownloadForm.vue'

    export default {
        name: "tickets-list"
        , components: {
            Vuetable
            , VuetablePaginationBootstrap
            , VuetablePaginationInfo
            , ticketForm
            , downloadForm

        }
        , data(){
            return{
                spinner: false
                , spinnerEvents: false
                , showTicketForm: false
                , showDownloadForm: false

                , css: BootstrapStyle

                , filter_status: ''
                , filter_ticket_id: ''
                //, event_id:0
                , event: {
                    event_id: 0
                    , name:''
                    , event_date: ''
                    , number_of_tickets:0
                    , number_of_ok:0
                    , number_of_redeemed:0
                }
                , download_type: 'all'
                , appendParams:{}
                , ticket_fields:[
                    {
                        name: 'ticket_id',
                        title: 'Ticket Code',
                        titleClass: 'text-left',
                        dataClass: 'text-left',
                        sortField: 'ticket_id'
                    }
                    ,{
                        name: 'updated_at',
                        title: 'Updated at',
                        titleClass: 'text-left',
                        dataClass: 'text-left',
                        sortField: 'updated_at'
                    }
                    ,{
                        name: '__slot:status',
                        title: 'Status',
                        titleClass: 'text-center',
                        dataClass: 'text-center',
                        sortField: 'status'

                    }
                ]
                , sortOrder: [
                    {
                        field: 'ticket_id',
                        sortField: 'ticket_id',
                        direction: 'asc'
                    }
                ]
            }
        }
        , watch: {
            filter_status: function (value) {
                this.search();
            }
        }
        , computed: {
            api_url: function(){
                return  '/api/events/' + this.$route.params.event_id + '/tickets';
            }
            , route_event: function(){
                return  parseInt(this.$route.params.event_id);
            }
        }
        , created() {
            // this.event_id = this.$route.params.event_id
            this.loadData();
        }
        , methods:{
            loadData(){
                var self = this;
                self.spinnerEvents=true;
                axios({
                    method:'GET'
                    , url: '/api/events/' + self.$route.params.event_id
                })
                    .then(function(response) {
                        // console.log('--- Response --');
                        // console.log(response);
                        self.spinnerEvents = false;
                        self.event=response.data;

                    })
                    .catch(e => {
                        self.spinnerEvents = false;
                        self.$message({
                            type:'error'
                            , message:e.response.data.error.message
                        });
                    });
            }
            , goBack(){
                this.$router.push('/events');
            }
            , vuetableLoadSuccess(response) {
                this.spinner = false;
            }
            , vuetableLoading() {
                this.spinner = true;
            }
            , onPaginationData(paginationData) {
                // console.log('onPaginationData' , paginationData);
                this.$refs.pagination.setPaginationData(paginationData)
                this.$refs.paginationInfo.setPaginationData(paginationData)
            }
            , onChangePage (page) {
                // this.loadData(this.url + '?page='+ page);
                this.$refs.vuetable.changePage(page)
            }
            , getIcon(status){
                return (status)? 'el-icon-success text-success' :  'el-icon-error text-danger';
            }
            , getToolTip(status){
                return (status)?  'OK' : 'Redeemed';
            }
            , getLink(action,id){
                if(action == 'tickets'){
                    return '/events/'+ id + '/tickets';
                }
                if(action == 'edit'){
                    return '/events/edit/'+ id;
                }
            }
            , download(type){
                this.download_type= type;
                this.showDownloadForm= true;
            }
            , search(){
                this.appendParams = {
                    filter_ticket_id: this.filter_ticket_id
                    , filter_status: this.filter_status
                }
                Vue.nextTick( () => this.$refs.vuetable.refresh());
            }
            , refresh(){
                let self = this;
                this.spinner = true;
                this.$refs.vuetable.reload()
                    .then(function () {
                        self.spinner = false;
                    })
                    .catch(e => {
                        self.spinner = false;
                        self.$message({
                            type:'error'
                            , message:e.response.data.error.message
                        });
                    });
            }
            , updateStatus(obj){
                console.log(obj);
                let url = '/api/tickets/' + obj.ticket_id + '/update';
                let data={
                    ticket_id: obj.ticket_id
                    , status: (obj.status)? 0: 1
                };
                let self = this;
                self.spinner=true;
                axios({
                    method:'POST'
                    , url: url
                    , data: data
                })
                    .then(function(response) {

                        self.spinner = false;
                        self.loadData();
                        self.refresh();
                        self.$message({
                            type: 'success',
                            message: 'Success update event'
                        });
                    })
                    .catch(e => {
                        self.spinner = false;
                        self.$message({
                            type:'error'
                            , message:e.response.data.error.message
                        });
                    });
            }
            , loadAddTicketForm(){
                this.showTicketForm=true;
            }
            , onAddTickets(){
                this.loadData();
                this.refresh();
            }
        }
    }
</script>

<style scoped>
 i{
    font-size: x-large !important;
 }
</style>