<?php
/**
 * Created by Linzh.
 * Email: linzhonghuang@pwrd.com
 * Date: 2019/3/19
 * Time: 17:31
 */

namespace {

    class Prox
    {

        private $request_url = '';
        private $request_fields = null;
        private $response_code = 0;
        private $response_content = '';

        /**
         * @return string
         */
        public function getRequestUrl(): string
        {
            return $this->request_url;
        }

        /**
         * @return null
         */
        public function getRequestFields()
        {
            return $this->request_fields;
        }

        /**
         * @return int
         */
        public function getResponseCode(): int
        {
            return $this->response_code;
        }

        /**
         * @return string
         */
        public function getResponseContent(): string
        {
            return $this->response_content;
        }

        /**
         * 发起get请求
         * @param string $url
         * @param array $header
         * @return void
         * @throws
         */
        public function doGet(string $url, array $header = []): void
        {
            $this->prepareRequest();
            $ch = curl_init($this->request_url = $url);
            if (stripos($url, 'https://') !== false) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSLVERSION, 1);
            }
            curl_setopt($ch, CURLOPT_HEADER, false); //将头文件的信息作为数据流输出
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, false);
            $header and curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            $this->execRequest($ch);
        }

        public function doPost(string $url, $fields, array $header = []): void
        {
            $this->prepareRequest();
            $ch = curl_init($this->request_url = $url);
            if (stripos($url, 'https://') !== false) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSLVERSION, 1);
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request_fields = $fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $header and curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $this->execRequest($ch);
        }

        private function prepareRequest()
        {
            $this->response_code = 0;
            $this->response_content = '';
        }

        private function execRequest($ch)
        {
            $this->response_content = (string)curl_exec($ch);
            $this->response_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        }


        public function readRawInput()
        {
            return (string)file_get_contents('php://input');
        }

        public function run()
        {
            $data = base64_decode($this->readRawInput());

            $data = json_decode($data, true);
            if (is_array($data) and isset($data['req_url'], $data['req_fields'], $data['req_options'])) {
                $req_url = $data['req_url'];
                $req_fields = $data['req_fields'];
                $req_options = $data['req_options'];
                if ($req_options) {
                    $req_options = json_decode($req_options, true);
                }
                if (!is_array($req_options)) {
                    $req_options = [];
                }
                $method = strtoupper($req_options['method'] ?? 'GET');
                $headers = $req_options['headers'] ?? [];
                switch (strtolower($req_options['data_type'] ?? '')) {
                    case 'json':
                        parse_str($req_fields, $_tmp);
                        $req_fields = json_encode($_tmp);
                        break;
                    case 'form-data': # 暂时不支持提交文件
                        parse_str($req_fields, $_tmp);
                        $req_fields = $_tmp;
                        break;
                    case 'x-www-form-urlencoded':
                    default:
                        # 默认为 x-www-form-urlencoded
                        break;
                }
                if ($method === 'POST') {
                    $this->doPost($req_url, $req_fields, $headers);
                } else {
                    $this->doGet($req_url . '?' . $req_fields, $headers);
                }
                echo json_encode([
                    "req_url" => $this->request_url,
                    "req_fields" => $this->request_fields,
                    "resp_code" => $this->response_code,
                    "resp_text" => $this->response_content,
                ]);
            }
        }
    }

    $prox = new Prox();
    $prox->run();
}