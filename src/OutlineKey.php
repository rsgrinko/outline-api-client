<?php

    namespace rsgrinko\Outline;

    use rsgrinko\Outline\Exceptions\OutlineApiException;
    use rsgrinko\Outline\Exceptions\OutlineException;
    use rsgrinko\Outline\Exceptions\OutlineKeyException;
    use rsgrinko\Outline\Exceptions\OutlineKeyNotFoundException;

    class OutlineKey
    {
        /** @var OutlineApiClient|null Client object */
        protected ?OutlineApiClient $api      = null;

        /** @var array $data Key data */
        protected array $data     = [
            'id'        => -1,
            'name'      => '',
            'password'  => '',
            'port'      => -1,
            'method'    => '',
            'accessUrl' => '',
        ];

        /** @var bool $isLoaded Flag is key loaded */
        protected bool $isLoaded = false;


        /**
         * @throws OutlineApiException
         */
        public function __construct(string $server)
        {
            $this->api = new OutlineApiClient($server);
        }

        /**
         * Set key data
         *
         * @param array $setData Key data
         */
        protected function setData(array $setData): void
        {
            $this->data = array_merge($this->data, $setData);
        }

        /**
         * Get key data
         *
         * @return array Key data
         */
        public function getData(): array
        {
            return $this->data;
        }

        /**
         * Checking is key loaded
         *
         * @return bool
         */
        protected function isLoaded(): bool
        {
            return $this->isLoaded;
        }

        /**
         * Get key
         *
         * @param int $keyId Key ID
         *
         * @throws OutlineException
         */
        public function get(int $keyId, string $searchKey = 'id'): array
        {
            $getKeyList  = $this->api->getKeys();
            $findKeyData = [];

            if (!empty($getKeyList)) {
                $list = $getKeyList['accessKeys'];

                foreach ($list as $item) {
                    if ($keyId === (int)$item[$searchKey]) {
                        $findKeyData = $item;
                        break;
                    }
                }

                if (empty($findKeyData)) {
                    throw new OutlineKeyNotFoundException('Key not found.');
                }
            } else {
                throw new OutlineKeyException('Not transferred keys list');
            }

            return $findKeyData;
        }


        /**
         * Get key by name
         *
         * @param string $name Name
         * @throws OutlineException
         */
        public function getByName(string $name): array
        {
            return $this->get($name, 'name');
        }

        /**
         * Load key
         *
         * @param int $keyId Key ID
         * @throws OutlineException
         */
        public function load(int $keyId): OutlineKey
        {
            $data = $this->get($keyId);
            $this->setData($data);
            $this->isLoaded = true;

            return $this;
        }

        /**
         * Get key ID
         *
         * @return int
         */
        public function getId(): int
        {
            return (int)$this->data['id'];
        }

        /**
         * Get key name
         *
         * @return string
         */
        public function getName(): string
        {
            return $this->data['name'];
        }


        /**
         * Get transfer stat
         *
         * @throws OutlineException
         */
        public function getTransfer(): int
        {
            $transfer = 0;

            $transferData = $this->api->metricsTransfer();

            if (isset($transferData['bytesTransferredByUserId'])
                && array_key_exists($this->getId(), $transferData['bytesTransferredByUserId'])) {
                $transfer = $transferData['bytesTransferredByUserId'][$this->getId()];
            }

            return (int)$transfer;
        }

        /**
         * Get traffic limit
         *
         * @return int
         */
        public function getLimit(): int
        {
            return (int)$this->data['dataLimit']['bytes'];
        }

        /**
         * Get access url
         *
         * @return string
         */
        public function getAccessUrl(): string
        {
            return $this->data['accessUrl'];
        }


        /**
         * @throws OutlineException
         */
        public function rename($newName)
        {
            if ($this->isLoaded()) {
                $setName = $this->api->setName($this->getId(), $newName);
                if (!$setName) {
                    throw new OutlineKeyException('Rename key error');
                } else {
                    $this->setData(['name' => $newName]);
                }
            } else {
                throw new OutlineKeyException('Failed rename key. Need load data key.');
            }
        }

        /**
         * @throws OutlineKeyException
         * @throws OutlineApiException
         */
        public function limit($limitValue)
        {
            if ($this->isLoaded()) {
                $setLimit = $this->api->setLimit($this->getId(), $limitValue);

                if (!$setLimit) {
                    throw new OutlineKeyException('Error set limit. Please contact administrator');
                } else {
                    $this->setData([
                                       'dataLimit' => [
                                           'bytes' => $limitValue,
                                       ],
                                   ]);
                }
            } else {
                throw new OutlineKeyException('Failed set limit for key. Need load data key');
            }
        }

        /**
         * @throws OutlineKeyException
         * @throws OutlineApiException
         */
        public function deleteLimit()
        {
            if ($this->isLoaded()) {
                $deleteLimit = $this->api->delete($this->getId());

                if (!$deleteLimit) {
                    throw new OutlineKeyException('Error delete key limit');
                } else {
                    $this->setData([
                                       'dataLimit' => [],
                                   ]);
                }
            } else {
                throw new OutlineKeyException('Failed delete limit for key. Need load data key');
            }
        }

        /**
         * @throws OutlineKeyException
         * @throws OutlineApiException
         */
        public function create(string $name, ?int $limit = null): OutlineKey
        {
            if (!empty($name)) {
                $create = $this->api->create();

                if (!empty($create)) {
                    $this->setData($create);

                    $setName = $this->api->setName($create['id'], $name);

                    if ($setName) {
                        $this->setData(['name' => $name]);

                        if ($limit !== null) {
                            $setLimit = $this->api->setLimit($create['id'], $limit);

                            if ($setLimit) {
                                $this->setData(
                                    [
                                        'dataLimit' => [
                                            'bytes' => $limit,
                                            ],
                                        ]
                                );
                            } else {
                                throw new OutlineKeyException('Error set limit key');
                            }
                        }
                    } else {
                        throw new OutlineKeyException('Error set key name');
                    }
                } else {
                    throw new OutlineKeyException('Error create key');
                }
            }

            return $this;
        }


        /**
         * @throws OutlineKeyException
         * @throws OutlineApiException
         */
        public function delete(): bool
        {
            if ($this->api->delete($this->getId())) {
                return true;
            } else {
                throw new OutlineKeyException('Error delete key id=' . $this->getId());
            }
        }
    }