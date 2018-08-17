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
} catch (Error $err) {
    echo "an error occured: $err";
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
    * [change_node](#change_node)
    * [fetch_epoch](#fetch_epoch)
    * [export](#export)
    * [import](#import)
* Oracle
    * [oracle_create](#oracle_create)
    * [oracle_delete](#oracle_delete)
    * [oracle_list](#oracle_list)
    * [oracle_pause](#oracle_pause)
    * [oracle_restart](#oracle_restart)
***
## FUNCTIONS

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
} catch (Error $err) {
    echo $err; // handle error
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
    $res = $qlite->fetch_epoch('UTDDHEBERHPZSPWMVI9LJIXPAANPWEWAEDWTXKXKJHGGCJKLYRIXIFTSL9JINSEIRDXDXALQPSXIT9MIZ', 4, 7);
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"last_completed_epoch":815,"duration":42,"fetched_epochs":[{"result":"49","quorum":2,"epoch":7,"quorum_max":3},{"quorum":1,"epoch":8,"quorum_max":3},{"result":"81","quorum":2,"epoch":9,"quorum_max":3}],"success":true}
```
***
### `export()`
transforms an entity (iam stream, qubic or oracle) into a string that can be imported again
#### parameters
| name | type | description |
| - | - | - |
| `$id`  | `string (trytes{81})` | id of the entity to export
#### example call
```php
try {
    $res = $qlite->export('ABPANJJKKATWFV9MAZMFXWMSHJKANRKA9BOXXENWXQVVPM9VUJWHPXUEN9GTRZCPNSZRBQCPQATFLEUQZ');
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"export":"q_OWOTGJ9Z9BUWNOCKAMNJEDMFTRTVVYCDOGKSWRPS9IJMNIFEBHXLQBGPV9GEBNRAGLSEKSBYZSLMYT999_GUACEZHVFAEZEYGUACEZGQFEFFGOAGHSDAHCFCEZGUACEZGDFAABABEYEVJVIDABGBJLFQGNICDRHUBCGSEEDWDZEOFPCDICHGEHHOEYCPGCHJAACCIBGKIZHPINHKGGIBETIJHHANIIESCLCRENCGGUEOCXBBIFJCDJABHFAAGBGYJFEKIWIQCDJBAZIABLBKBFBFEAFCJRFOGGCOHZCHBPDJEWCDCSFZEQHFIHDZCSBOBMFTFNFCETADEODFCRGCCPFAGZIEFRIKFUARGWEOJLELBUGPIRDJGOEHEKGGFBFXBDDDHSEZCTFAFTEYAXIQIAAPFTGHFJCYBYASCFACBIEDAEFJEIIIGAENFAABABEYEPDTBGAFDIBBHHDQCXCIBRIMHACEIHCFJPAUBVCHESHEECACERIHHWFJHHFFACIXIBIJIHAOCGDGIJHZDYJHFFFOABAACAHTFUJHGHEAHWGMFUFRCDDBFHGWAMCUBMDTHGFUJQALIEJSANGMDSBJBUGCGPBZBMJLARJEBJJVFJESGFGZISEJETISJQEZGIHFCYBKEJCKBOIBAQAJBOADDRDTIKDXBFFEASALIWIOAAJRIFGJIUEZHWHFEWDBHTGOFCFUFAFSHHHGJVJKDHESJQABAUIRDJGRIXDIETAHAHJFBDJA"}
```
***
### `import()`
imports a once exported entity (iam stream, qubic or oracle) encoded by a string
#### parameters
| name | type | description |
| - | - | - |
| `$encoded`  | `string (string)` | the code you received from command 'export' (starts with 'i_', 'o_' or 'q_')
#### example call
```php
try {
    $res = $qlite->import('q_HZGULHJSZNDWPTOCXDYYKMKXCCKCHPORELEBZLBQRWHQNBMNAHBGWQYD9WRVHFKRQRXUXLXORJEPTN999_GUACEZHWFAEZEYGUACEZGQFEFFGOAGHSDAHCFCEZGUACEZGDFAABABEYEVJVIDABGBJLFQGNICDRHUBCGSEEDWDZEOFPCDICHGEHHOEYCPGCHJAACCIBGKIZHPINHKGGIBETIJHHANIIESCLCRENCGGUEOCXBBIFJCDJABHFAAGBGYJFEKIWIQCDJBAZIABLBKBFBFEAFCJRFOGGCOHZCHBPDJEWCDCSFZEQHFIHDZCSBOBMFTFNFCETADEODFCRGCCPFAGZIEFRIKFUARGWEOJLELBUGPIRDJGOEHEKGGFBFXBDDDHSEZCTFAFTEYAXIQIAAPFTGHFJCYBYASCFACBIEDAEFJEIIIGAENFAABABEYEPDTBGAFDIBBHHDQCXCIBRIMHACEIHCFJPAUBVCHESHEECACERIHHWFJHHFFACIXIBIJIHAOCGDGIJHZDYJHFFFOABAACAHTFUJHGHEAHWGMFUFRCDDBFHGWAMCUBMDTHGFUJQALIEJSANGMDSBJBUGCGPBZBMJLARJEBJJVFJESGFGZISEJETISJQEZGIHFCYBKEJCKBOIBAQAJBOADDRDTIKDXBFFEASALIWIOAAJRIFGJIUEZHWHFEWDBHTGOFCFVFAFTEYAHDRGKJTIPFGBHHIGIDNAABHFEEEGEAYJIIVFKDD');
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
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
    $res = $qlite->qubic_read('PZLDKHNMXW9JFCBNZEMTEGECSHPTW9WVXYPMAS9INNREKLUOULZXRWAXZSCGSEUNOAYREPCCJFBFK9VVT');
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"assembly_list":["N9HTFCGRFZATJCRLTEAINRYXTACXENTWAHLIEOCNBMQKTXHO9BIZIJGNVGDPRLFCUBQUNOEKIKN9AOGRT"],"duration":42,"code":"return(epoch^2);","hash_period_duration":20,"result_period_duration":10,"success":true,"runtime_limit":10,"execution_start":1534489591,"id":"RLTVANLUEBZCKZTCPPRZEURLIWPEDQFLGIJBQVCDUWSMQUYZZSXCZZYEYMDJNAFXSFSJMRDDYFFPVP999","version":"ql-0.4-SNAPSHOT"}
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
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"list":[{"specification":{"code":"return('hello world');","hash_period_duration":20,"result_period_duration":10,"execution_start":1534489593,"run_time_limit":10,"type":"qubic transaction","version":"ql-0.4-SNAPSHOT"},"id":"CHDMRSGEICAAGHSECBJPBZEOYWVGLRVCZGKRFEOYGOCQGMIH9FMRWIOS9JLWVUD9OZY9PLGRM9SAJV999","state":"assembly phase"}]}
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
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"qubic_id":"VHKJTYJDYMHEXDJUEXPURLBHEQODXSBPKTKPMBMVCXQDQFZ9DZVAYBLSXIAHQRRWMIGDQNGJTPJZNBYDS"}
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
    $res = $qlite->qubic_delete('QQSKVFLJZSIBXRZJAYZBYGIFZXAJHDWYUTQAZFSGWONVNHCYJFVKZBTVJOFQFQWVHZJUUVFGQWZOHTLUP');
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
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
    $res = $qlite->qubic_list_applications('UAUZKHQLWXFKNYEZCBQQ9PMKMJAVTOMBQNMKXHVNFHSVCSGWUGHANMJZSGKTWMMW9YULGFSODDUZYSOSV');
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"list":["HYJKQZYTQHUNFDUIKMFTJSG9AUMNBBNRZBOAINEWTDSFLPIQPOMIBP9JCEHXVWHEJSTPQYZBSMMAAAUBX","ELOP9CNHBUUKWZCSDRDDKZLKAYCNMZOYQEHWDQXKDAYKGAPZRBPMX9MXCEUSAPHQEMCJGU9OSFRVVCCEQ"]}
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
    $res = $qlite->qubic_assemble('ULJ9UEUQVSZOBVXUDTYDIUMNJAJHNTSUGIERZPDBCKJ9NEQJ9VJYNWAEQWXPITPJTQYGHWWNREXFPGHSQ', ['SIIJAYWFNTLOMXROYIKLYQAEVRKUYPNSXMPYGQQNVWBXZZUETYHVHPUQPEBG9QR9NGPKBPBYJFGPFFLVA', 'KCJFNTG9ZNGATGDJOPHUIUBPDNAHSKKZYFY99Y9FBRCFROIZWVRQWRIW9WGKMGXGIYEUGEGVVMTSLPMAN']);
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
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
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"result":"9","duration":42,"success":true,"runtime":12}
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
    $res = $qlite->oracle_create('L9NRZRGSKCPQAWKYIJZBWTVWCP9JKGFJWTBYHFPXRVKZFGTT9C9KLMNWYUJGUPBNEOA9CASSS9HT9GCYH');
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"oracle_id":"HIWPHZRPRFGNWOXFZXIZUQYNDEEUWPQDCPLXEYRF9IQ9FGRGOKSWBPOOBDHJUUTTWMJRPU9IUTCZMDGGJ","success":true}
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
    $res = $qlite->oracle_delete('PGQUECEDNTPYB9IBDTAJVPKVOXTUQUNQYISDOBWFZOJWYZXPMCALCVYF9PKWCHFDVKZAMFXIAG9IDAKQS');
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
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
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"list":["HSARKOIRDXGSZVEXTQMH9IJZMIGHZWFQINMYNGDMARKVULABMOICPJFZGVUMCJGXUFZGQBXLBLFZNOVTH","GNMHLHXQZADIKJNB9VNEC9OHXXYKM9WMRUCPRFVYBDPVODNVPPCKOMCLBWIJQWETGUDJCQOTTCRKCDOZ9"]}
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
    $res = $qlite->oracle_pause('YSRHDPOHGWTNJAUSQTSUQPKWTFDHZLOQCITUZ9BJGZDEIBUJDOWIDMJIMWLRYSLFOKFGJCXWICPRLVDJX');
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
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
| `$id`  | `string (trytes{81})` | oracle ID
#### example call
```php
try {
    $res = $qlite->oracle_restart('AACIBQW9X9MOCEMYHYFIVDUDHAQDWJIVZZADJCMJILKICJLABCIRQLZFXYMCKVPYSVXJZXEZD9VZDTLOA');
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
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
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"iam_id":"CZHRMAYPROWQTFXTNLOGBPQQFOEOYAGKSFXUZD9VGWCAQVZKP9BQNGSETGIZZTXMVMATLKJK9YJPTHQ9Z","success":true}
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
} catch (Error $err) {
    echo $err; // handle error
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
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true,"list":["VBBUFDZVA9YGONQRCDOMOX9MPVMOCNYPXNQJAHTYPTJSMIXI9HBUTOJGUGJFCHIAMRHLFIN9QZHDUQVAM","PGCWWNYSLTVPOXHRJQIJIAKSTTKOMQQIUNCNBJOKHUFMGNZBZSVNFEULXBQOOSIIAFNAIHCITA9NENRB9"]}
```
***
### `iam_write()`
Writes a message into the iam stream at an index position.
#### parameters
| name | type | description |
| - | - | - |
| `$id`  | `string (trytes{81})` | the IAM stream in which to write
| `$index`  | `int (integer{0-2147483647})` | index at which to write
| `$message`  | `object (jsonobject)` | the json object to write into the stream
#### example call
```php
try {
    $res = $qlite->iam_write('CLUZILAWASDZAPQXWQHWRUBNXDFITUDFMBSBVAGB9PVLWDSYADZBPXCIOAYOEYAETUUNHNW9R9TZKU999', 17, array('day' => 4));
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
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
| `$index`  | `int (integer{0-2147483647})` | index from which to read the message
#### example call
```php
try {
    $res = $qlite->iam_read('CLUZILAWASDZAPQXWQHWRUBNXDFITUDFMBSBVAGB9PVLWDSYADZBPXCIOAYOEYAETUUNHNW9R9TZKU999', 17);
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
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
} catch (Error $err) {
    echo $err; // handle error
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
} catch (Error $err) {
    echo $err; // handle error
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
| `$app`  | `string (alphanumeric)` | app ID (directory name in 'qlweb/qlweb-0.4.0/qapps')
#### example call
```php
try {
    $res = $qlite->app_uninstall('tanglefarm');
    // process $res ...
} catch (Error $err) {
    echo $err; // handle error
}
```
#### example response (before being parsed into a php array)
```json
{"duration":42,"success":true}
```
***
