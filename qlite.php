<?php

class Qlite {

    private static $VERSION = "0.4.1";

    private $qlnode_url;

    public function __construct($qlnode_url) {
        $this->qlnode_url = $qlnode_url;
    }

    private function send_request($request) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
        curl_setopt($ch, CURLOPT_URL, $this->qlnode_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-QLITE-API-Version: ' . Qlite::$VERSION));
        $answer = json_decode(curl_exec ($ch), true);
        curl_close($ch);

        if(!$answer['success']) {
            throw new Exception($answer['error']);
        }

        return $answer;
    }

    private static function validate_tryte_sequence($value, $par_name, $min_length, $max_length) {
        Qlite::validate_string($value, $par_name);
        if(!preg_match('/^[A-Za-z9]*$/', $value))
            throw new Exception("parameter '".$par_name."' contains illegal characters, only trytes (A-Z,9) are allowed");
        if (strlen($value) < $min_length)
            throw new Exception("length of parameter '".$par_name."' (".strlen($value).") is less than allowed minimum ($min_length)");
        if (strlen($value) > $max_length)
            throw new Exception("parameter '".$par_name."' (".strlen($value).") is greater than allowed maximum ($max_length)");

    }

    private static function validate_integer($value, $par_name, $min, $max) {
        if(!is_numeric($value))
            throw new Exception("parameter '".$par_name."' is not a number");
        if ($value < $min)
            throw new Exception("parameter '".$par_name."' (= $value) is less than allowed minimum: $min");
        if ($value > $max)
            throw new Exception("parameter '".$par_name."' (= $value) is greater than allowed maximum: $max");
    }

    private static function validate_string($value, $par_name) {
        if(!is_string($value))
            throw new Exception("parameter '".$par_name."' is not a string");
    }

    private static function validate_array($value, $par_name) {
        if(!is_array($value) || substr(json_encode($value), 0, 1) !== '[')
            throw new Exception("parameter '".$par_name."' is not an array");
    }

    private static function validate_object($value, $par_name) {
        if(!is_array($value) || substr(json_encode($value, true), 0, 1) !== '{')
            throw new Exception("parameter '".$par_name."' is not an object");
    }

    private static function validate_alphanumeric($value, $par_name) {
        Qlite::validate_string($value, $par_name);
        if(!ctype_alnum($value))
            throw new Exception("parameter '".$par_name."' contains illegal characters, only alphanumeric characters (a-z, 0-9) are allowed");
    }

    
    /**
     * Gives details about this ql-node.
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","iota_node":"https://nodes.devnet.thetangle.org:443","success":true,"testnet":true,"version":"0.5.0"}
     * */
    public function node_info() {
        $request = array('command' => 'node_info');
        return $this->send_request($request);
    }

    /**
     * Changes the IOTA full node used to interact with the tangle.
     * @param string $node_address address of any IOTA full node api (mainnet or testnet, depending on which ql-nodes you want to be able to interact with), e.g. 'https://node.example.org:14265'
     * @param int $mwm (optional) min weight magnitude: use 9 when connecting to a testnet node, otherwise use 14, e.g. 14
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function change_node($node_address, $mwm = 14) {
        $this->validate_string($node_address, '$node_address');
        $mwm = (int)$mwm; $this->validate_integer($mwm, '$mwm', 9, 14);
        $request = array('command' => 'change_node', 'node address' => $node_address, 'mwm' => $mwm);
        return $this->send_request($request);
    }

    /**
     * Determines the quorum based result (consensus) of a qubic's epoch.
     * @param string $qubic qubic to fetch from, e.g. 'CIVZQYMDDDHVBTYFWUEQQNSGCNO9TMVPGOSCEXEYEKMMUUTWXRVJNGHRAJZYJUFUVVRQLTITDCNTQZTNX'
     * @param int $epoch epoch to fetch, e.g. 4
     * @param int $epoch_max (optional) if used will fetch all epochs from 'epoch' up to this value, e.g. 7
     * @return array decoded from json, unparsed success example:
     *     {"last_completed_epoch":815,"duration":"42","fetched_epochs":[{"result":"49","quorum":2,"epoch":7,"quorum_max":3},{"quorum":1,"epoch":8,"quorum_max":3},{"result":"81","quorum":2,"epoch":9,"quorum_max":3}],"success":true}
     * */
    public function fetch_epoch($qubic, $epoch, $epoch_max = -1) {
        $this->validate_tryte_sequence($qubic, '$qubic', 81, 81);
        $epoch = (int)$epoch; $this->validate_integer($epoch, '$epoch', 0, 2147483647);
        $epoch_max = (int)$epoch_max; $this->validate_integer($epoch_max, '$epoch_max', -1, 2147483647);
        $request = array('command' => 'fetch_epoch', 'qubic' => $qubic, 'epoch' => $epoch, 'epoch max' => $epoch_max);
        return $this->send_request($request);
    }

    /**
     * Transforms an entity (iam stream, qubic or oracle) into a string that can be imported again.
     * @param string $id id of the entity to export, e.g. 'YVLICKIQDN9DDWPYCSHQAIRUDJWOQWDDIAXIKSIRFFO9HNQMYGJWBWQIFCLRRDRJDNNYQUZWNABZXVGY9'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true,"export":"q_BGBKIXLBEUTKP9FEMEGPQPSDTPLOZBXJARRPCTHOWYFPMQQ9JZZJCYVRUCIBNSKF9HPRYCYFCTRXLJ999_GUACEZHVFAEZEYGUACEZGQFEFFGOAGHSDAHCFCEZGUACEZGDFAABABEYEVJVIDABGBJLFQGNICDRHUBCGSEEDWDZEOFPCDICHGEHHOEYCPGCHJAACCIBGKIZHPINHKGGIBETIJHHANIIESCLCRENCGGUEOCXBBIFJCDJABHFAAGBGYJFEKIWIQCDJBAZIABLBKBFBFEAFCJRFOGGCOHZCHBPDJEWCDCSFZEQHFIHDZCSBOBMFTFNFCETADEODFCRGCCPFAGZIEFRIKFUARGWEOJLELBUGPIRDJGOEHEKGGFBFXBDDDHSEZCTFAFTEYAXIQIAAPFTGHFJCYBYASCFACBIEDAEFJEIIIGAENFAABABEYEPDTBGAFDIBBHHDQCXCIBRIMHACEIHCFJPAUBVCHESHEECACERIHHWFJHHFFACIXIBIJIHAOCGDGIJHZDYJHFFFOABAACAHTFUJHGHEAHWGMFUFRCDDBFHGWAMCUBMDTHGFUJQALIEJSANGMDSBJBUGCGPBZBMJLARJEBJJVFJESGFGZISEJETISJQEZGIHFCYBKEJCKBOIBAQAJBOADDRDTIKDXBFFEASALIWIOAAJRIFGJIUEZHWHFEWDBHTGOFCFUFAFSJEHVIAHIDFFUCTJRIPJNAOAJCLIUDCGHADGIDRGM"}
     * */
    public function export($id) {
        $this->validate_tryte_sequence($id, '$id', 81, 81);
        $request = array('command' => 'export', 'id' => $id);
        return $this->send_request($request);
    }

    /**
     * Imports a once exported entity (iam stream, qubic or oracle) encoded by a string.
     * @param string $encoded the code you received from command 'export' (starts with 'i_', 'o_' or 'q_'), e.g. 'q_HZGULHJSZNDWPTOCXDYYKMKXCCKCHPORELEBZLBQRWHQNBMNAHBGWQYD9WRVHFKRQRXUXLXORJEPTN999_GUACEZHWFAEZEYGUACEZGQFEFFGOAGHSDAHCFCEZGUACEZGDFAABABEYEVJVIDABGBJLFQGNICDRHUBCGSEEDWDZEOFPCDICHGEHHOEYCPGCHJAACCIBGKIZHPINHKGGIBETIJHHANIIESCLCRENCGGUEOCXBBIFJCDJABHFAAGBGYJFEKIWIQCDJBAZIABLBKBFBFEAFCJRFOGGCOHZCHBPDJEWCDCSFZEQHFIHDZCSBOBMFTFNFCETADEODFCRGCCPFAGZIEFRIKFUARGWEOJLELBUGPIRDJGOEHEKGGFBFXBDDDHSEZCTFAFTEYAXIQIAAPFTGHFJCYBYASCFACBIEDAEFJEIIIGAENFAABABEYEPDTBGAFDIBBHHDQCXCIBRIMHACEIHCFJPAUBVCHESHEECACERIHHWFJHHFFACIXIBIJIHAOCGDGIJHZDYJHFFFOABAACAHTFUJHGHEAHWGMFUFRCDDBFHGWAMCUBMDTHGFUJQALIEJSANGMDSBJBUGCGPBZBMJLARJEBJJVFJESGFGZISEJETISJQEZGIHFCYBKEJCKBOIBAQAJBOADDRDTIKDXBFFEASALIWIOAAJRIFGJIUEZHWHFEWDBHTGOFCFVFAFTEYAHDRGKJTIPFGBHHIGIDNAABHFEEEGEAYJIIVFKDD'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function import($encoded) {
        $this->validate_string($encoded, '$encoded');
        $request = array('command' => 'import', 'encoded' => $encoded);
        return $this->send_request($request);
    }

    /**
     * Reads the specification of any qubic, thus allows the user to analyze that qubic.
     * @param string $qubic id of the qubic to read, e.g. 'YZXSGDLDRTFAFIBDPCBBXYEIHZRWELXVGDINCQLIBEBZDBY9KWZMFOURIXPXHXPPSQTFRSRQXIGZNMRVS'
     * @return array decoded from json, unparsed success example:
     *     {"assembly_list":["RIFRDCDBZUECJQSTPUIPMOONUDVBCKPNFXNGJTTNJWELTLPPXOH9B9JNAWPFWZXMJAQJZCATKR9CLHUL9"],"duration":"42","code":"return(epoch^2);","hash_period_duration":20,"result_period_duration":10,"success":true,"runtime_limit":10,"execution_start":1537595169,"id":"VNQKWHDEDTCZVJQSMMLUPUAU9AALKEARI9CHWCYFXQ9VWNPZWHWNNMKZPBPAWVRHMTYSBVR9SZPYFS999","version":"ql-0.5.0"}
     * */
    public function qubic_read($qubic) {
        $this->validate_tryte_sequence($qubic, '$qubic', 81, 81);
        $request = array('command' => 'qubic_read', 'qubic' => $qubic);
        return $this->send_request($request);
    }

    /**
     * Lists all qubics stored in the persistence.
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true,"list":[{"specification":{"code":"return('hello world');","hash_period_duration":20,"result_period_duration":10,"execution_start":1537595170,"run_time_limit":10,"type":"qubic transaction","version":"ql-0.5.0"},"id":"RKR9PYBNREGTFAERMXTD9ZOBWSHHFFXELG9ILH9LKQFEXPCZK9WUIELNSPHQVYAPDRYPUXRNNSHXXT999","state":"assembly phase"}]}
     * */
    public function qubic_list() {
        $request = array('command' => 'qubic_list');
        return $this->send_request($request);
    }

    /**
     * Creates a new qubic and stores it in the persistence. life cycle will not be automized: do the assembly transaction manually.
     * @param int $execution_start amount of seconds until (or unix timestamp for) end of assembly phase and start of execution phase, e.g. 300
     * @param int $hash_period_duration amount of seconds each hash period (first part of the epoch) lasts, e.g. 30
     * @param int $result_period_duration amount of seconds each result period (second part of the epoch) lasts, e.g. 30
     * @param int $runtime_limit maximum amount of seconds the QLVM is allowed to run per epoch before aborting (to prevent endless loops), e.g. 10
     * @param string $code the qubic code to run, e.g. 'return(epoch^2);'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true,"qubic_id":"PEAXNFYKMHHDZTVABHGMVZDEZJCZRYYBL9KDDXIBZBAGEZXCFXUZULWPROGZAEOYAIUDVSXARUOLWCVO9"}
     * */
    public function qubic_create($execution_start, $hash_period_duration, $result_period_duration, $runtime_limit, $code) {
        $execution_start = (int)$execution_start; $this->validate_integer($execution_start, '$execution_start', 1, 2147483647);
        $hash_period_duration = (int)$hash_period_duration; $this->validate_integer($hash_period_duration, '$hash_period_duration', 1, 2147483647);
        $result_period_duration = (int)$result_period_duration; $this->validate_integer($result_period_duration, '$result_period_duration', 1, 2147483647);
        $runtime_limit = (int)$runtime_limit; $this->validate_integer($runtime_limit, '$runtime_limit', 1, 2147483647);
        $this->validate_string($code, '$code');
        $request = array('command' => 'qubic_create', 'execution start' => $execution_start, 'hash period duration' => $hash_period_duration, 'result period duration' => $result_period_duration, 'runtime limit' => $runtime_limit, 'code' => $code);
        return $this->send_request($request);
    }

    /**
     * Removes a qubic from the persistence (private key will be deleted: cannot be undone).
     * @param string $qubic deletes the qubic that starts with this tryte sequence, e.g. '9C9KCKTFAJZK9OFIHEECGFUWYESO99SOXZMCGCOWXGVQQIOSPJZI9ZKZFGLE9KGZ9DLJLYR9DOQULSIRO'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function qubic_delete($qubic) {
        $this->validate_tryte_sequence($qubic, '$qubic', 81, 81);
        $request = array('command' => 'qubic_delete', 'qubic' => $qubic);
        return $this->send_request($request);
    }

    /**
     * Lists all incoming oracle applications for a specific qubic, response can be used for 'qubic_assembly_add'.
     * @param string $qubic the qubic of which you want to list all applications, e.g. 'ETATZZETOKMM9FWPHUPUNXUUXTPBQYTIUPKFFYKQVPBIPSCVDOYKLRQVBXDNRQWXTUEQB9HYFNASYDDVU'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true,"list":["FJSYCRISJHUZOUGZDML9NQNZLCBFPEEBDJFHZSNIHVIJPCRXWJPDG9JBGKFOWSHQFSOFQY9MCX9EHYMJQ","99QSSBAMFHJZRAJPNV9LJUJOHDDGZ9VXEC9IYPABZYWIENIFIVLJBQPKYOW9O9RGLPCPKQ9BWHPUUOUHI"]}
     * */
    public function qubic_list_applications($qubic) {
        $this->validate_tryte_sequence($qubic, '$qubic', 81, 81);
        $request = array('command' => 'qubic_list_applications', 'qubic' => $qubic);
        return $this->send_request($request);
    }

    /**
     * Publishes the assembly transaction for a specific qubic.
     * @param string $qubic the qubic that shall publish its assembly transaction, e.g. 'FUBNEIMBGQXCYMIVDOHQWYILWTJRRUNXQJBHUCIBWTMEDWQPHCN9CLOVKUFRNANTGJCXZTFI9RDKHTQA9'
     * @param array $assembly json array of the oracle IDs to be part of the assembly, e.g. ['HRDLRALANLBTHBHVWYPXBA9WOTTITVTNAOVG9VRINYSTCAHAMTOTGCRDCFMTRXGCSZFILIWSQRD9NJQQQ', 'LBJPOGAQFWHKGBHLJPM9RWDOUCDXLCWFSLADDBUDBQFAZROZLQZDKRXRIVQTLWWNQGYYDFNB99MBUYHQY']
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function qubic_assemble($qubic, $assembly) {
        $this->validate_tryte_sequence($qubic, '$qubic', 81, 81);
        $this->validate_array($assembly, '$assembly');
        $request = array('command' => 'qubic_assemble', 'qubic' => $qubic, 'assembly' => $assembly);
        return $this->send_request($request);
    }

    /**
     * Runs QL code locally (instead of over the tangle) to allow the author to quickly test whether it works as intended. Limited Functionality (e.g. no qubic_fetch).
     * @param string $code qubic code you want to test, e.g. 'return(epoch^2)'
     * @param int $epoch_index (optional) initializes the run time variable 'epoch' to simulate a running qubic, e.g. 3
     * @return array decoded from json, unparsed success example:
     *     {"result":"9","duration":"42","success":true,"runtime":12}
     * */
    public function qubic_test($code, $epoch_index = 0) {
        $this->validate_string($code, '$code');
        $epoch_index = (int)$epoch_index; $this->validate_integer($epoch_index, '$epoch_index', 0, 2147483647);
        $request = array('command' => 'qubic_test', 'code' => $code, 'epoch index' => $epoch_index);
        return $this->send_request($request);
    }

    /**
     * Determines the quorum based consensus of a qubic's oracle assembly at any IAM index.
     * @param string $qubic qubic to find consensus in, e.g. 'ZFIZEDOYVKAZYTTCBEV9LGBSPOIWDAGMGTUKOASOGUKPDQQAVQBJFMNTAXSXAHHI9IQFAWNRMHXTATBEY'
     * @param string $keyword keyword of the iam index to find consensus for, e.g. 'W'
     * @param int $position position of the iam index to find consensus for, e.g. 4
     * @return array decoded from json, unparsed success example:
     *     {"result":"{'color': 'red'}","duration":"42","success":true,"index_keyword":"COLORS","quorum":3,"quorum_max":4,"index_position":2018}
     * */
    public function qubic_consensus($qubic, $keyword, $position) {
        $this->validate_tryte_sequence($qubic, '$qubic', 81, 81);
        $this->validate_tryte_sequence($keyword, '$keyword', 0, 30);
        $position = (int)$position; $this->validate_integer($position, '$position', 0, 2147483647);
        $request = array('command' => 'qubic_consensus', 'qubic' => $qubic, 'keyword' => $keyword, 'position' => $position);
        return $this->send_request($request);
    }

    /**
     * Creates a new oracle and stores it in the persistence. Life cycle will run automically, no more actions required from here on.
     * @param string $qubic ID of the qubic which shall be processed by this oracle., e.g. 'QHOPEZLOALZPKEVROWVSWJBPNXRJLJATBCIKGOOBEBERJMBAPGA9MWBQ9XVJZOLYMICFCNXCKGVWXUMAD'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","oracle_id":"CZMV9BMIHBRUJWIHHAJJLHQ9LMWMJDVZIYIODQLPWK9AETIATVRAGV9AZ9DV9NRQSCBRRPHOFAIWYUKMR","success":true}
     * */
    public function oracle_create($qubic) {
        $this->validate_tryte_sequence($qubic, '$qubic', 81, 81);
        $request = array('command' => 'oracle_create', 'qubic' => $qubic);
        return $this->send_request($request);
    }

    /**
     * Removes an oracle from the persistence (private key will be deleted, cannot be undone).
     * @param string $id oracle ID, e.g. 'ICYFZZS9JCMPYDDTE9BZ9FBOLPUZDQLTYHYKNMN9JLLGD9VDSDTY9AYKE9KAOWLMUPSNLAWNFQYIBMRJY'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function oracle_delete($id) {
        $this->validate_tryte_sequence($id, '$id', 81, 81);
        $request = array('command' => 'oracle_delete', 'id' => $id);
        return $this->send_request($request);
    }

    /**
     * Lists all oracles stored in the persistence
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true,"list":["QMZPI9UBOHGDDTXOKGXAUO9UTAWVALNZCGGJZOGRFXYJMISTYLCONHNYKECTNGATBADOYQZXTVLH9U9LD","KSNGWECMOPDWWEXLKZYTFJRRPCKVOM9PLYRCHSHTM9CNXSIFHCBKXPUWJGSEGQQLAPUVUXJMQTCTMUK9H"]}
     * */
    public function oracle_list() {
        $request = array('command' => 'oracle_list');
        return $this->send_request($request);
    }

    /**
     * Temporarily stops an oracle from processing its qubic after the epoch finishes. Can be undone with 'oracle_restart'.
     * @param string $id oracle ID, e.g. 'EOJIZIEVRP9GFQAAFZDEWDFGMGBKMLYXDODJUIUURONWICKPVLPKEKLQFJBBHEPJRESIIYRRRRVNHKGKP'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function oracle_pause($id) {
        $this->validate_tryte_sequence($id, '$id', 81, 81);
        $request = array('command' => 'oracle_pause', 'id' => $id);
        return $this->send_request($request);
    }

    /**
     * Restarts an oracle that was paused with 'oracle_pause', makes it process its qubic again.
     * @param string $id restarts the oracle that starts with this tryte sequence, e.g. 'DVCZGOL9POLHMOZYBHFEGYRIQBGDYTHXZWVYVSEJKXCY9LFLLXSVSYMJUGYMROTYKHWGJUIWKCKHUWRSA'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function oracle_restart($id) {
        $this->validate_tryte_sequence($id, '$id', 81, 81);
        $request = array('command' => 'oracle_restart', 'id' => $id);
        return $this->send_request($request);
    }

    /**
     * Creates a new IAM stream and stores it locally in the persistence.
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","iam_id":"JRYGMAEWZWIDCERMDPWJGCWIGLGLRMPBQBDXVRIOZSPTJTCFY99YSDGQQMYHGWYCRBVLX9GISPUWEVFZI","success":true}
     * */
    public function iam_create() {
        $request = array('command' => 'iam_create');
        return $this->send_request($request);
    }

    /**
     * Removes an IAM stream from the persistence (private key will be deleted, cannot be undone).
     * @param string $id IAM stream ID, e.g. 'XUYRQFPGFAMCNNRE9BMGYDWNTXLKWQBYYECSMZAMQFGHTUHSIYKVPDOUOCTUKQPMRGYF9IJSXIMKMAEL9'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function iam_delete($id) {
        $this->validate_tryte_sequence($id, '$id', 81, 81);
        $request = array('command' => 'iam_delete', 'id' => $id);
        return $this->send_request($request);
    }

    /**
     * List all IAM streams stored in the persistence.
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true,"list":["KVN9ILICRPDGWCPCYXAAOKCXLURBKSKYOSQTGHQJBQDYEWPAR9UZ9NBASKFJRMACEAGIDLJUECWBRFTJQ","HSRVQRJWMBWMBEMIMNPWOIUTSIDAHSRENYDWDI9UGENTAHGTXICEJBUDPTTBNENWEX9HKGLXYDTEUSAOZ"]}
     * */
    public function iam_list() {
        $request = array('command' => 'iam_list');
        return $this->send_request($request);
    }

    /**
     * Writes a message into the iam stream at an index position.
     * @param string $id the IAM stream in which to write, e.g. 'CLUZILAWASDZAPQXWQHWRUBNXDFITUDFMBSBVAGB9PVLWDSYADZBPXCIOAYOEYAETUUNHNW9R9TZKU999'
     * @param int $index position of the index at which to write, e.g. 17
     * @param object $message the json object to write into the stream, e.g. {'day': 4}
     * @param string $keyword (optional) keyword of the index at which to write, e.g. 'ADDRESS'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function iam_write($id, $index, $message, $keyword = '') {
        $this->validate_tryte_sequence($id, '$id', 81, 81);
        $index = (int)$index; $this->validate_integer($index, '$index', 0, 2147483647);
        $this->validate_object($message, '$message');
        $this->validate_tryte_sequence($keyword, '$keyword', 0, 30);
        $request = array('command' => 'iam_write', 'ID' => $id, 'index' => $index, 'message' => $message, 'keyword' => $keyword);
        return $this->send_request($request);
    }

    /**
     * Reads the message of an IAM stream at a certain index.
     * @param string $id IAM stream you want to read, e.g. 'CLUZILAWASDZAPQXWQHWRUBNXDFITUDFMBSBVAGB9PVLWDSYADZBPXCIOAYOEYAETUUNHNW9R9TZKU999'
     * @param int $index position of index from which to read the message, e.g. 17
     * @param string $keyword (optional) keyword of index from which to read the message, e.g. 'RESULTS'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","read":{"habit":"antarctica","name":"penguin"},"success":true}
     * */
    public function iam_read($id, $index, $keyword = '') {
        $this->validate_tryte_sequence($id, '$id', 81, 81);
        $index = (int)$index; $this->validate_integer($index, '$index', 0, 2147483647);
        $this->validate_tryte_sequence($keyword, '$keyword', 0, 30);
        $request = array('command' => 'iam_read', 'id' => $id, 'index' => $index, 'keyword' => $keyword);
        return $this->send_request($request);
    }

    /**
     * Lists all apps installed.
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true,"list":[{"license":"&copy;2018 by microhash for qame.org","description":"Grow and harvest food on your own farm. The first qApp and decentralized IOTA game. The game state is entirely stored on the Tangle and validated by Qubic Lite.","id":"tanglefarm","title":"Tangle Farm","version":"v0.1","url":"http://qame.org/tanglefarm"}]}
     * */
    public function app_list() {
        $request = array('command' => 'app_list');
        return $this->send_request($request);
    }

    /**
     * Installs an app from an external source.
     * @param string $url download source of the app, e.g. 'http://qame.org/tanglefarm'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function app_install($url) {
        $this->validate_string($url, '$url');
        $request = array('command' => 'app_install', 'url' => $url);
        return $this->send_request($request);
    }

    /**
     * Uninstalls an app.
     * @param string $app app ID (directory name in 'qlweb/qlweb-0.5.1/qapps'), e.g. 'tanglefarm'
     * @return array decoded from json, unparsed success example:
     *     {"duration":"42","success":true}
     * */
    public function app_uninstall($app) {
        $this->validate_alphanumeric($app, '$app');
        $request = array('command' => 'app_uninstall', 'app' => $app);
        return $this->send_request($request);
    }
}

?>
