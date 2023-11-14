<?php

    use rsgrinko\Outline\{Exceptions\OutlineException, OutlineApiClient, OutlineKey};
    use rsgrinko\Outline\Exceptions\OutlineApiException;


    /**
     * Outline facade class
     */
    class Outline
    {
        private OutlineApiClient $clientObject;
        private OutlineKey $keyObject;

        /**
         * @throws OutlineApiException
         */
        public function __construct(string $url)
        {
            $this->clientObject = new OutlineApiClient($url);
            $this->keyObject    = new OutlineKey($url);
        }

        /**
         * Get OutlineApiClient object
         *
         * @return OutlineApiClient
         */
        public function getClientObject(): OutlineApiClient
        {
            return $this->clientObject;
        }

        /**
         * Get OutlineKey object
         *
         * @param int $id Key ID
         * @throws OutlineException
         */
        public function getKeyObject(int $id): OutlineKey
        {
            return $this->keyObject->load($id);
        }


    }