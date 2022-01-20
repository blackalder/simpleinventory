<template>
    <div class="row">
        <button class="btn" @click="add">Add</button>
        <vue-suggestion :items="customers" v-model="form.customer_id" , :setLabel="setLabel" , :itemTemplate="itemTemplate" , @onInputChange="inputChange" , @onItemSelected="itemSelected">
        </vue-suggestion>
        <modal name="addmodal" transition="pop-out" :width="modalWidth" :focus-trap="true" :height="500">
            <div class="box">
                <div class="partition" id="partition-register">
                    <div class="partition-title">SALES</div>
                    <div class="partition-form">
                        <form autocomplete="false">
                            <input id="n-trx" type="text" placeholder="Code Transaksi" v-model="form.code_transaksi">
                            <select id="n-tipe" name="unit" v-model="form.customer_id" placeholder="Unit">
                            <option selected value="">Pilih User</option>
                                          <option v-for="item in listCustomers" :value="item.id" :key="item.id">
                  {{ item.nama }}
                </option>
                                                                </select>
    
                            <div class="form-group row" v-for="(item, k) in form.items" :key="k">
                                <div class="col-md-5">
                                <select id="n-tipe" name="unit" v-model="form.items[k].item_id" placeholder="Unit">
                                <option selected value="">Pilih Item</option>
                                          <option v-for="item in listItems" :value="item.id" :key="item.id">
                  {{ item.nama_item }}
                </option></select>
                                </div>
                                <div class="col-md-5">
                                    <input id="item" type="number" class="form-control" name="qty" v-model="form.items[k].qty" placeholder="Qty">
                                </div>
                            </div>
                            <div class="col-md-12 text-right">
                                <p-button type="info" outline icon><i class="fa fa-plus" @click.prevent="addMore()"></i></p-button>
                            </div>
    
                        </form>
                        <div class="col-md-12 text-center"><button class="btn btn-info" @click="submit">Submit</button></div>
    
    
                    </div>
                </div>
            </div>
        </modal>
        <div class="col-12">
            <card :title="title">
                <div slot="raw-content" class="table-responsive">
                    <table class="table">
                        <thead>
                            <slot name="columns">
                                <th v-for="column in tableColumns" :key="column">{{ column }}</th>
                            </slot>
                        </thead>
                        <tbody>
                            <template v-for="(item, index) in tableData">
                                                                                  <tr>
                                                                                    <td>{{item.id}}</td>
                                                                                    <td>{{item.code_transaksi}}</td>
                                                                                    <td>{{item.tanggal_transaksi}}</td>
                                                                                    <td>{{item.customer.nama}}</td>
                                                                                    <td>{{item.total_diskon}}</td>
                                                                                    <td><b>{{item.total_bayar}}</b></td>
                                                                                    
                                                                                    <td>{{new Date(item.updated_at).toLocaleString('en-GB')}}</td>
                                                                                    <td>
                                                                                      <p-button type="danger" outline icon><i class="fa fa-trash" @click="remove(item.id)"></i></p-button>
                                                                                    </td>
                                                                                  </tr>
                                                                                  <tr style="border:0">
                                                                                    <td colspan=7>
                                                                                      <table width=100% border=0 style="background:#f1f1f1;">
                                                                                        <tr v-for="(item, index) in item.items">
                                                                                          <td>{{item.nama_item}}</td>
                                                                                          <td><img v-bind:src="apiurl+item.barang" style="width:70px"></td>
                                                                                          <td>{{item.qty}}({{item.unit}})</td>
                                                                                          <td>{{item.harga_satuan}}</td>
                                                                                          <td>{{item.total_harga}}</td>
                                                                                        </tr>
                                                                                      </table>
                                                                                    </td>
                                                                                  </tr>
</template>
              
              
            </tbody>
          </table>
        </div>
      </card>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import VueSuggestion from 'vue-suggestion'
import itemTemplate from './ItemTemplate.vue';

const tableColumns = ["Id", "Code TRX", "tanggal", "customer name", "Diskon", "Total", "updated", "action"];
const apiurl = "http://127.0.0.1:8000";
const MODAL_WIDTH = 656

let tableData = [];
let stateform = 'add';

export default {
    components: {
        VueSuggestion
    },
    mounted() {
        this.getData();
        this.getCustomers();
        this.getItems();
    },
    methods: {
        getData() {
            axios.get(apiurl + '/api/sales')
                .then((result) => {
                    this.tableData = result.data.data;
                    console.log(result);
                }).catch((err) => {
                    console.log(err);
                });
        },
        getItems() {
            axios.get(apiurl + '/api/items')
                .then((result) => {
                    this.listItems = result.data.data;
                }).catch((err) => {
                    console.log(err);
                });
        },
        getCustomers() {
            axios.get(apiurl + '/api/customers')
                .then((result) => {
                    this.listCustomers = result.data.data;
                }).catch((err) => {
                    console.log(err);
                });
        },
        addMore() {
            this.form.items.push({
                item_id: '',
                qty: 0
            });
        },
        add() {
            stateform = 'add';
            this.$modal.show('addmodal');
            this.form = {
                code_transaksi: '',
                customer_id: '',
                items: []
            };
        },
        submit() {
            let url = apiurl + '/api/sales';
            let method = "POST";

            if (stateform == 'update') {
                url += '/' + this.form.id;
                method = "PUT";
            }

            if(this.form.customer_id == '') return alert('pilih customerid');
            if(this.form.items[0].item_id == '') return alert('pilih item');
            axios({
                    method: method,
                    url: url,
                    data: this.form
                })
                .then((res) => {
                    alert(res.data.message);
                    this.getData();
                })
                .catch((error) => {
                    alert(JSON.stringify(error.response.data));
                }).finally(() => {
                    //Perform action in always
                });
        },
        remove(id) {
            console.log(id);
            axios.delete(apiurl + '/api/sales/' + id)
                .then((result) => {
                    alert(result.data.message)
                    console.log(result);
                    this.getData();
                }).catch((err) => {
                    console.log(err);
                });
        },
        itemSelected(item) {
            this.item = item;
        },
        setLabel(item) {
            return item.name;
        },
        inputChange(text) {
            // your search method
            this.items = items.filter(item => item.name.contains(text));
            // now `items` will be showed in the suggestion list
        },
    },

    data() {
        return {
            form: {
                code_transaksi: '',
                customer_id: '',
                items: []
            },
            listCustomers: [],
            listItems: [],
            tableData,
            tableColumns,
            title: "Sales",
            modalWidth: MODAL_WIDTH,
            apiurl,
            customers: [
                { id: 1, name: 'Golden Retriever' },
                { id: 2, name: 'Cat' },
                { id: 3, name: 'Squirrel' },
            ],
            itemTemplate,
        };
    },
};
</script>

<style>

</style>
