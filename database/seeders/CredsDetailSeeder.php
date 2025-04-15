<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CredsDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('creds_details')->delete();
        $data = array('creds_for' => 'quickbook' ,
			'auth_mode' => 'oauth2' ,
			'quickbook_invoice_hour_id' => '16' ,
			'quickbook_invoice_load_id' => '17' ,
			'quickbook_invoice_tax_code_ref' => '7' ,
			'quickbook_invoice_tax_rate_ref' => '12' ,
			'quickbook_sale_term_ref' => '7' ,
			'client_id' => 'ABuW6NkZi3rVpLUfx6iN9l8USY9R637E8yI8752Cg691gJNcTA' ,
			'client_secret'=>'sCSbn1dPbC5Bco7u1ilqUcpAPaVTXvYvlF5D8nrw',
			'redirect_uri' => 'http://127.0.0.1/JapGobindTransportMasterBranch/callback-quickbook-response' ,
			'refresh_token' => 'AB11690024600wPb6R9qInMkyI79nrQwQCIJlKQceHNjJJTY4s',
			'realm_id' => '4620816365296084950',
			'type' => 'development',
			'use_quickbook_api' => 'inactive',
			'use_own_system_opposite_quickbook' => 'active',
			'access_token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..4k7NA7k3ROfQNuYqQkwYHw.zp4XZfJfhCkEENQ4kADvk-bA33a8kYC4u0j9JmtJsHpYc5vTSeLKQan5yMwJ9l7VBtQWwmquGz0iyjFGm3rNwKhupMXyFU3v77lY-OLoYV_S2ZzYZjEyw8odMr6kDdYuqIx0w7E3Xufvo69sHpPq3ikBOM97cFn4i9BxVBXw14pirvFDDXdlhnVdQwHeZgN0v-IjHijCSJK08DhiRlW2RCrkAYB31H5kW6wNEDE2iX-fmwN1had4cCNbyNMcIYDj743pNjgvRZpI5VaZ0duZRsBKnaphEIozkCUM9gvOu7q423pjQsBli5jsK4F5MnOsVJUPj_8Y7VP5AMcVEq-hr4i1SQ1D0221nXu_3y9FHN1-oVY3S-4PtQ8czceWRitEDqKe-20TeJg559fDxscDeY13iX34nlniFMEtOndIbypcdFFn0Y6othe8UN_S8KbZFsezpXbz_zfdbqDXuT4Igd2ml3JYxKtokJvX6UG_j2XQOCbkBg9LzAqfKMzK4QoIGzXojhT3SjlUCoE-1nb92UBolQp_bYlE59p2gAqlmZkFUDwfSzZ30sFbX7EHziUnYIieB8GSvkm2J7QW_r9ObwecWKOaDe3p32C94L2ycbTp649_p64QGYM1yvhM-SQMPCg_AIrFZeikFGO2jdQdArgvlGqFzo_dRRHIg8DOhPe8tSPjk74n6WTahGjBRtG22DCDitUBugB7_AriKbSQwcKXFOvuECk6FM6_ZNrgKMEzHBrklejLxGvH4d0APmAtb8g4t3k-GpId5cObh-Vdb2STeMbwA2p5ZqlgX3TmK34tJqn5zKVNvm3Z83Mxf3xWn7CjqPDdqX3zJfHJ3U3KIdAZaUP4F2Nu_Q9w3OFtpPzRLdGf7jGk1nLzvP9KeEuh.Cc5CxUU1YojwfUPKGiyu3w',			
        );
        DB::table('creds_details')->insert($data);
    }
}
