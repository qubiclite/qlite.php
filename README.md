# qlite.php

## ABOUT

This library allows you to connect to existing ql-nodes and use their API to interact with the Qubic Lite protocol.

## USE

### 1. download `qlite.php` and put it into your project's directory

### 2. include the library into your .php file

```php
include('../qlite.php');
```

### 3. initialize a new qlite instance in your php script

```php
$qlite = new Qlite('https://qlnode.org:17733');
```

### 4. send requests

```php
$my_qubic = "GAPGBVIBDKTGZ9BVLCZYWPZAFMIXBDLCUTXOC9NEJ9HGDKZYGRPQVIHMZXRXCDLZIFXGECZBFSTTNA999";

try {
    $res = $qlite->qubic_read($my_qubic);
    echo "qubic code: $res[code]";
} catch (Exception $exc) {
    echo "an exception occured: $exc";
}
```
# API DOCUMENTATION

* App
    * [app_list](#app_list)
    * [app_install](#app_install)
    * [app_uninstall](#app_uninstall)
* Qubic
    * [qubic_read](#qubic_read)
    * [qubic_list](#qubic_list)
    * [qubic_create](#qubic_create)
    * [qubic_delete](#qubic_delete)
    * [qubic_list_applications](#qubic_list_applications)
    * [qubic_assemble](#qubic_assemble)
    * [qubic_test](#qubic_test)
* IAM
    * [iam_create](#iam_create)
    * [iam_delete](#iam_delete)
    * [iam_list](#iam_list)
    * [iam_write](#iam_write)
    * [iam_read](#iam_read)
* General
    * [node_info](#node_info)
    * [change_node](#change_node)
    * [fetch_epoch](#fetch_epoch)
    * [export](#export)
    * [import](#import)
    * [qubic_consensus](#qubic_consensus)
* Oracle
    * [oracle_create](#oracle_create)
    * [oracle_delete](#oracle_delete)
    * [oracle_list](#oracle_list)
    * [oracle_pause](#oracle_pause)
    * [oracle_restart](#oracle_restart)
***
## FUNCTIONS

### `node_info()`
Gives details about this ql-node.
#### parameters
no parameters
#### example call
```php
try {
    $res = $qlite->node_info();
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"iota_node":"https://nodes.devnet.thetangle.org:443","success":true,"testnet":true,"version":"0.5.0"}
```
***
### `change_node()`
Changes the IOTA full node used to interact with the tangle.
#### parameters
| name | type | description |
| - | - | - |
| `$node_address`  | `string (nodeaddress)` | address of any IOTA full node api (mainnet or testnet, depending on which ql-nodes you want to be able to interact with)
| `$mwm`  (opt.) | `int (integer{9-14})` | min weight magnitude: use 9 when connecting to a testnet node, otherwise use 14
#### example call
```php
try {
    $res = $qlite->change_node('https://node.example.org:14265', 14);
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
### `fetch_epoch()`
Determines the quorum based result (consensus) of a qubic's epoch.
#### parameters
| name | type | description |
| - | - | - |
| `$qubic`  | `string (trytes{81})` | qubic to fetch from
| `$epoch`  | `int (integer{0-2147483647})` | epoch to fetch
| `$epoch_max`  (opt.) | `int (integer{-1-2147483647})` | if used will fetch all epochs from 'epoch' up to this value
#### example call
```php
try {
    $res = $qlite->fetch_epoch('RKSECWM9WKAIZPNPCBVDAEONYYRENWCYQWESPOIWWORHAACWXBCOCCMMWIFVL9GDZFRIHZYOMVQMARMWN', 4, 7);
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"last_completed_epoch":815,"duration":42,"fetched_epochs":[{"result":"49","quorum":2,"epoch":7,"quorum_max":3},{"quorum":1,"epoch":8,"quorum_max":3},{"result":"81","quorum":2,"epoch":9,"quorum_max":3}],"success":true}
```
***
### `export()`
Transforms an entity (iam stream, qubic or oracle) into a string that can be imported again.
#### parameters
| name | type | description |
| - | - | - |
| `$id`  | `string (trytes{81})` | id of the entity to export
#### example call
```php
try {
    $res = $qlite->export('SJVNTLHKPIPDDIMFFADOLIZHECADWDLBJGRXEYWYWHFSBZDAWUCHRHUCSLUUVDPIJVWFZTQ9KMFSIUMDT');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"export":"q_OBMJB9OIQVCRAZMLLTSOULXXTBPQIHBXB9WZNDYLNBZSDSCURGEXUQWEYNEXYMJWUXXBKWQOPINB9D999_GUACEZHWFAEZEYGUACEZGQFEFFGOAGHSDAHCFCEZGUACEZGDFAABABEYEVJVIDABGBJLFQGNICDRHUBCGSEEDWDZEOFPCDICHGEHHOEYCPGCHJAACCIBGKIZHPINHKGGIBETIJHHANIIESCLCRENCGGUEOCXBBIFJCDJABHFAAGBGYJFEKIWIQCDJBAZIABLBKBFBFEAFCJRFOGGCOHZCHBPDJEWCDCSFZEQHFIHDZCSBOBMFTFNFCETADEODFCRGCCPFAGZIEFRIKFUARGWEOJLELBUGPIRDJGOEHEKGGFBFXBDDDHSEZCTFAFTEYAXIQIAAPFTGHFJCYBYASCFACBIEDAEFJEIIIGAENFAABABEYEPDTBGAFDIBBHHDQCXCIBRIMHACEIHCFJPAUBVCHESHEECACERIHHWFJHHFFACIXIBIJIHAOCGDGIJHZDYJHFFFOABAACAHTFUJHGHEAHWGMFUFRCDDBFHGWAMCUBMDTHGFUJQALIEJSANGMDSBJBUGCGPBZBMJLARJEBJJVFJESGFGZISEJETISJQEZGIHFCYBKEJCKBOIBAQAJBOADDRDTIKDXBFFEASALIWIOAAJRIFGJIUEZHWHFEWDBHTGOFCFVFAFTEYAGBWJEIODBJNGZCPCEBAGMIQIZAMIKIWGFBRITBA"}
```
***
### `import()`
Imports a once exported entity (iam stream, qubic or oracle) encoded by a string.
#### parameters
| name | type | description |
| - | - | - |
| `$encoded`  | `string (string)` | the code you received from command 'export' (starts with 'i_', 'o_' or 'q_')
#### example call
```php
try {
    $res = $qlite->import('q_HZGULHJSZNDWPTOCXDYYKMKXCCKCHPORELEBZLBQRWHQNBMNAHBGWQYD9WRVHFKRQRXUXLXORJEPTN999_GUACEZHWFAEZEYGUACEZGQFEFFGOAGHSDAHCFCEZGUACEZGDFAABABEYEVJVIDABGBJLFQGNICDRHUBCGSEEDWDZEOFPCDICHGEHHOEYCPGCHJAACCIBGKIZHPINHKGGIBETIJHHANIIESCLCRENCGGUEOCXBBIFJCDJABHFAAGBGYJFEKIWIQCDJBAZIABLBKBFBFEAFCJRFOGGCOHZCHBPDJEWCDCSFZEQHFIHDZCSBOBMFTFNFCETADEODFCRGCCPFAGZIEFRIKFUARGWEOJLELBUGPIRDJGOEHEKGGFBFXBDDDHSEZCTFAFTEYAXIQIAAPFTGHFJCYBYASCFACBIEDAEFJEIIIGAENFAABABEYEPDTBGAFDIBBHHDQCXCIBRIMHACEIHCFJPAUBVCHESHEECACERIHHWFJHHFFACIXIBIJIHAOCGDGIJHZDYJHFFFOABAACAHTFUJHGHEAHWGMFUFRCDDBFHGWAMCUBMDTHGFUJQALIEJSANGMDSBJBUGCGPBZBMJLARJEBJJVFJESGFGZISEJETISJQEZGIHFCYBKEJCKBOIBAQAJBOADDRDTIKDXBFFEASALIWIOAAJRIFGJIUEZHWHFEWDBHTGOFCFVFAFTEYAHDRGKJTIPFGBHHIGIDNAABHFEEEGEAYJIIVFKDD');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
### `qubic_read()`
Reads the specification of any qubic, thus allows the user to analyze that qubic.
#### parameters
| name | type | description |
| - | - | - |
| `$qubic`  | `string (trytes{81})` | id of the qubic to read
#### example call
```php
try {
    $res = $qlite->qubic_read('GJS9IHSVQQZNVSBPOTF9O9MSAXGEVPATUOZCRONJTI9LSQSU9NXSODAUKTHN9LKGIQKXXMOITOISZRXTA');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"assembly_list":["HBZJWXRXYLXM9AHNB9TTCWPTINNABCOPOW9XTZPLKJPZUWPZPKDZQL9XYZAYYOYOMAJGI9MNFWWNA9XNG"],"duration":42,"code":"return(epoch^2);","hash_period_duration":20,"result_period_duration":10,"success":true,"runtime_limit":10,"execution_start":1537595256,"id":"JKPVMXCTZTAUXRAXF9LQYZHERSTCCLLRMFMJWTNK9URRXKRGWTADXOCWRRAJNA9FQKRHOWDCKYPPYI999","version":"ql-0.5.0"}
```
***
### `qubic_list()`
Lists all qubics stored in the persistence.
#### parameters
no parameters
#### example call
```php
try {
    $res = $qlite->qubic_list();
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"list":[{"specification":{"code":"return('hello world');","hash_period_duration":20,"result_period_duration":10,"execution_start":1537595259,"run_time_limit":10,"type":"qubic transaction","version":"ql-0.5.0"},"id":"OWGGVWVX9SVMVAY9PJQIQUCXGYLNOSSOSMJBBZOXHIJYRAVDMGSWLIWWAOBVDMDANQLZMMMCXMLRNF999","state":"assembly phase"}]}
```
***
### `qubic_create()`
Creates a new qubic and stores it in the persistence. life cycle will not be automized: do the assembly transaction manually.
#### parameters
| name | type | description |
| - | - | - |
| `$execution_start`  | `int (integer{1-2147483647})` | amount of seconds until (or unix timestamp for) end of assembly phase and start of execution phase
| `$hash_period_duration`  | `int (integer{1-2147483647})` | amount of seconds each hash period (first part of the epoch) lasts
| `$result_period_duration`  | `int (integer{1-2147483647})` | amount of seconds each result period (second part of the epoch) lasts
| `$runtime_limit`  | `int (integer{1-2147483647})` | maximum amount of seconds the QLVM is allowed to run per epoch before aborting (to prevent endless loops)
| `$code`  | `string (string)` | the qubic code to run
#### example call
```php
try {
    $res = $qlite->qubic_create(300, 30, 30, 10, 'return(epoch^2);');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"qubic_id":"OSHCZ9UMFYSYFHY9FJZ9JUVVMTHPGQLZOGKVZFMDRAOKOVGT9SPONLZDJNROHMVFNMNEZPHPQNFVKZFHN"}
```
***
### `qubic_delete()`
Removes a qubic from the persistence (private key will be deleted: cannot be undone).
#### parameters
| name | type | description |
| - | - | - |
| `$qubic`  | `string (trytes{81})` | deletes the qubic that starts with this tryte sequence
#### example call
```php
try {
    $res = $qlite->qubic_delete('9RVVQZM9CKFAIQCOXMLSSGTXTPQEYZDQHFHOMSNOCGFWFZVOIQTBIVDMWEOTUKJQQLNEFS9YY9DSOWQQQ');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
### `qubic_list_applications()`
Lists all incoming oracle applications for a specific qubic, response can be used for 'qubic_assembly_add'.
#### parameters
| name | type | description |
| - | - | - |
| `$qubic`  | `string (trytes{81})` | the qubic of which you want to list all applications
#### example call
```php
try {
    $res = $qlite->qubic_list_applications('9XKIPOGHRURQNLMILDJZTOU9UGXBZRWNOYGLUDLINMCPELYCX9JEFCRRSZTG9SHZCOROYKTQXORKTQQVD');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"list":["VJBTIZUYIQIJWZWYUE9AXZXTARECXRFAANOGXESUTXQEXCUMHNBLGGMQCADFUAM9PAEPTMWXTNE9NMBFF","JUMLSWSOTVCQUJ9HRQ9ZRHHBRC99GJPFDFWPRZDQZEBMIHMSCRJGYKQQRDCHKLHMOL9FPIMVIRKKEBWSP"]}
```
***
### `qubic_assemble()`
Publishes the assembly transaction for a specific qubic.
#### parameters
| name | type | description |
| - | - | - |
| `$qubic`  | `string (trytes{81})` | the qubic that shall publish its assembly transaction
| `$assembly`  | `array (jsonarray)` | json array of the oracle IDs to be part of the assembly
#### example call
```php
try {
    $res = $qlite->qubic_assemble('AVEMIZGBLKVEC9C9JOUKHPBDGVYUNOYEXZONAKBUQMCAIYZQGYNEWOEEH9JFRVRWHPT9BBBNUEBSIZPVC', ['UJEMEYZFVHRPKUQPKHBHLIAKETIDRU9TVHYHYTSOMTNIMCYB9ZHOTFLIYNTBMGTLERAKHOVIUKKTNSQGV', 'LHBSKYJOAEDQBITWKEBANK9BMCDDHBOYBMD9XSFEXAIQFBGRRCTNVFTNP9HUYQWBBLIYZLBUSZHHZEJTQ']);
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
### `qubic_test()`
Runs QL code locally (instead of over the tangle) to allow the author to quickly test whether it works as intended. Limited Functionality (e.g. no qubic_fetch).
#### parameters
| name | type | description |
| - | - | - |
| `$code`  | `string (string)` | qubic code you want to test
| `$epoch_index`  (opt.) | `int (integer{0-2147483647})` | initializes the run time variable 'epoch' to simulate a running qubic
#### example call
```php
try {
    $res = $qlite->qubic_test('return(epoch^2)', 3);
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"result":"9","duration":42,"success":true,"runtime":12}
```
***
### `qubic_consensus()`
Determines the quorum based consensus of a qubic's oracle assembly at any IAM index.
#### parameters
| name | type | description |
| - | - | - |
| `$qubic`  | `string (trytes{81})` | qubic to find consensus in
| `$keyword`  | `string (trytes{0-30})` | keyword of the iam index to find consensus for
| `$position`  | `int (integer{0-2147483647})` | position of the iam index to find consensus for
#### example call
```php
try {
    $res = $qlite->qubic_consensus('ZRCWBIDGHKOXUJMGSTGWGWF9KZALDYUBGTNOCFQRWRAEKWSFHRJFFIQYOSQLJT9QJVZEKMXOCMEZLMBUD', 'L', 4);
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"result":"{'color': 'red'}","duration":42,"success":true,"index_keyword":"COLORS","quorum":3,"quorum_max":4,"index_position":2018}
```
***
### `oracle_create()`
Creates a new oracle and stores it in the persistence. Life cycle will run automically, no more actions required from here on.
#### parameters
| name | type | description |
| - | - | - |
| `$qubic`  | `string (trytes{81})` | ID of the qubic which shall be processed by this oracle.
#### example call
```php
try {
    $res = $qlite->oracle_create('QTGMQILKCUGDFELXIDLWKYOBQDSFGCXPITTNK9XXWELJN9AWLWBNGLTBZWXSYTK9QSAJTTTFUK9VRVAIG');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"oracle_id":"EIT9OBGLPOE9AULSUTJMZAMUYDCDJFBOAMMDRUOVYHJLWMIPVKARATEWVSVYIQHPTBUPJCLNJDSOBYQPH","success":true}
```
***
### `oracle_delete()`
Removes an oracle from the persistence (private key will be deleted, cannot be undone).
#### parameters
| name | type | description |
| - | - | - |
| `$id`  | `string (trytes{81})` | oracle ID
#### example call
```php
try {
    $res = $qlite->oracle_delete('PIMLCQXIUGDELO9KP9WYKWJSLJYGHCCGRHGLKA9EIFXUOXYINLREWGQMBYFKDBHLPFSGBPZGOOEDPWKGU');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
### `oracle_list()`
Lists all oracles stored in the persistence
#### parameters
no parameters
#### example call
```php
try {
    $res = $qlite->oracle_list();
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"list":["UXZGKWZZWABVH9VUQNFINGEWQCAYNVHGTHTBWQFY9DYIRCFSHOJSMUPGSDHIBNWUYLQNSHHLROCNSSNEX","TAXSBHU9JEFZS9MNTCWDAOCKVXDUUAJPUHONTJPBRMUKF9RRQSB99NZOHFTJPAMTVXBWKNYEXAZAUHMTO"]}
```
***
### `oracle_pause()`
Temporarily stops an oracle from processing its qubic after the epoch finishes. Can be undone with 'oracle_restart'.
#### parameters
| name | type | description |
| - | - | - |
| `$id`  | `string (trytes{81})` | oracle ID
#### example call
```php
try {
    $res = $qlite->oracle_pause('GTWWMHONBSK9QBEIFUJBOH9XXKNWYDNAVKDKJSVJIW9UTFXGGJDFDTIPSIQNEOXALTXMDORLEPEPKFLBQ');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
### `oracle_restart()`
Restarts an oracle that was paused with 'oracle_pause', makes it process its qubic again.
#### parameters
| name | type | description |
| - | - | - |
| `$id`  | `string (trytes{81})` | restarts the oracle that starts with this tryte sequence
#### example call
```php
try {
    $res = $qlite->oracle_restart('ISOLNKK9FFPMDZTT9IGOL9XXCDF9IJMQAEMXABLCGACQMINDSNLEGHHSTZDCIACWIRXC9GSRMASMRJZZJ');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
### `iam_create()`
Creates a new IAM stream and stores it locally in the persistence.
#### parameters
no parameters
#### example call
```php
try {
    $res = $qlite->iam_create();
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"iam_id":"CYBTQYRSSYKWFU9VCVZMHBBRUNQQQMDZWZXFAOZN9AORIPNKBCRBUYBRMZCHOKEZY9JVWFBRIQVEGSNC9","success":true}
```
***
### `iam_delete()`
Removes an IAM stream from the persistence (private key will be deleted, cannot be undone).
#### parameters
| name | type | description |
| - | - | - |
| `$id`  | `string (trytes{81})` | IAM stream ID
#### example call
```php
try {
    $res = $qlite->iam_delete('XUYRQFPGFAMCNNRE9BMGYDWNTXLKWQBYYECSMZAMQFGHTUHSIYKVPDOUOCTUKQPMRGYF9IJSXIMKMAEL9');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
### `iam_list()`
List all IAM streams stored in the persistence.
#### parameters
no parameters
#### example call
```php
try {
    $res = $qlite->iam_list();
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"list":["EAHFRRZAWYUYBG9XU9TNIBRJLTAIAFLWMVTFPUNYNPMVPKQBTDOPARUHVELPKWQGCSCZNCHXWIHODXYGM","TFPUVQE9AFVWZMSUIIVYUHXKFVFJDWGTBWCPKYLXSKGSZGWSWSCXDVJRPYCO99ZMWPLGGIX9DQANZFGVR"]}
```
***
### `iam_write()`
Writes a message into the iam stream at an index position.
#### parameters
| name | type | description |
| - | - | - |
| `$id`  | `string (trytes{81})` | the IAM stream in which to write
| `$index`  | `int (integer{0-2147483647})` | position of the index at which to write
| `$message`  | `object (jsonobject)` | the json object to write into the stream
| `$keyword`  (opt.) | `string (trytes{0-30})` | keyword of the index at which to write
#### example call
```php
try {
    $res = $qlite->iam_write('CLUZILAWASDZAPQXWQHWRUBNXDFITUDFMBSBVAGB9PVLWDSYADZBPXCIOAYOEYAETUUNHNW9R9TZKU999', 17, {'day': 4}, 'ADDRESS');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
### `iam_read()`
Reads the message of an IAM stream at a certain index.
#### parameters
| name | type | description |
| - | - | - |
| `$id`  | `string (trytes{81})` | IAM stream you want to read
| `$index`  | `int (integer{0-2147483647})` | position of index from which to read the message
| `$keyword`  (opt.) | `string (trytes{0-30})` | keyword of index from which to read the message
#### example call
```php
try {
    $res = $qlite->iam_read('CLUZILAWASDZAPQXWQHWRUBNXDFITUDFMBSBVAGB9PVLWDSYADZBPXCIOAYOEYAETUUNHNW9R9TZKU999', 17, 'RESULTS');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"read":{"habit":"antarctica","name":"penguin"},"success":true}
```
***
### `app_list()`
Lists all apps installed.
#### parameters
no parameters
#### example call
```php
try {
    $res = $qlite->app_list();
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"list":[{"license":"&copy;2018 by microhash for qame.org","description":"Grow and harvest food on your own farm. The first qApp and decentralized IOTA game. The game state is entirely stored on the Tangle and validated by Qubic Lite.","id":"tanglefarm","title":"Tangle Farm","version":"v0.1","url":"http://qame.org/tanglefarm"}]}
```
***
### `app_install()`
Installs an app from an external source.
#### parameters
| name | type | description |
| - | - | - |
| `$url`  | `string (url)` | download source of the app
#### example call
```php
try {
    $res = $qlite->app_install('http://qame.org/tanglefarm');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
### `app_uninstall()`
Uninstalls an app.
#### parameters
| name | type | description |
| - | - | - |
| `$app`  | `string (alphanumeric)` | app ID (directory name in 'qlweb/qlweb-0.5.1/qapps')
#### example call
```php
try {
    $res = $qlite->app_uninstall('tanglefarm');
    // process $res ...
} catch (Exception $exc) {
    echo $exc; // handle exception
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
