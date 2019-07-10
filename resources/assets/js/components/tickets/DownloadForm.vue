
<style scoped>
    .download-form .table-data{
        overflow-y: scroll;
        overflow-x: hidden;
        max-height: 450px;
    }
</style>

<template>
    <div class="download-form">
        <div class="card card-default">
            <div class="card-header">
                <el-row :gutter="20">
                    <el-col :span="18">
                        <h4>{{title}}</h4>
                    </el-col>
                    <el-col :span="6" class="text-right">
                        <download-csv
                                :data="jsonData"
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

            <div class="card-body"
                 v-loading="spinner"
                 element-loading-text="Loading data from Server...">
                <div class="table-data">
                    <table class="table table-striped">
                        <thead>
                            <th v-for="(value, name) in labels">{{ value }}</th>
                        </thead>
                        <tbody>
                            <tr  v-for="item in jsonData">
                                <td>{{item.index}}</td>
                                <td>{{item.event_date}}</td>
                                <td>{{item.event_name}}</td>
                                <td>{{item.ticket_id}}</td>
                                <td>{{item.status}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "download-form"
        , data(){
            return {
                spinner: false
                , jsonData: []
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
                type: Number,
                required: true
            }
            , type:{
                type: String,
                default: 'all',
                required: false
            }

        }
        , computed: {
            dataFile: function(){
                return  'tickets_event_'+ this.event_id+ '.csv';
            }
            , title: function(){
                let title = ''
                switch (this.type) {
                    case 'ok':
                        title= 'Tickets List Status: OK' ;
                        break;
                    case 'redeemed':
                        title= 'Tickets List Status: Redeemed' ;
                        break;
                    default:
                        title= 'Tickets List' ;

                }
                return title;
            }
        }
        , created() {
            this.loadData();
        }
        , methods:{
            loadData(){
                let url ='/api/events/' + this.event_id+'/tickets/';
                switch (this.type) {
                    case 'ok':
                    case 'redeemed':
                        url =url + this.type;
                        break;
                    default:
                        url =url + 'all';

                }
                var self = this;
                self.spinner=true;
                axios({
                    method:'GET'
                    , url: url
                })
                    .then(function(response) {
                        self.spinner = false;
                        self.fillJsonData(response.data.data);

                    })
                    .catch(e => {
                        self.spinner = false;
                        self.$message({
                            type:'error'
                            , message:e.response.data.error.message
                        });
                    });
            }
            , fillJsonData(data){
                let self =this;
                data.forEach(function(el) {
                    let item={
                        index: self.jsonData.length +1
                        , event_date: el.event_date
                        , event_name: el.event_name
                        , ticket_id: el.ticket_id
                        , status: (el.status)? 'OK' :  'Redeemed'
                    };
                    self.jsonData.push(item);

                });
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
