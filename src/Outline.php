<?php

    namespace rsgrinko\Outline;

    use rsgrinko\Outline\Exceptions\OutlineException;


    /**
     * Outline facade class
     */
    class Outline
    {
        private OutlineApiClient $clientObject;
        private OutlineKey $keyObject;

        /**
         * @throws OutlineException
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