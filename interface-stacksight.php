<?php

interface iStacksight {

	/**
	 * Make initialization app on MEAN network
	 * @param  string $name  app name
	 * @param  string $token access token
	 * @return [type]        [description]
	 */
    public function initApp($name, $token);

    /**
     * [publishEvent description]
     * @param  array $data {
	 *  key: 'articles',
	 *  name: 'view',
	 *  design: {
	 *      color: '#8FD5FF',
	 *      icon: 'fa-file-text'
	 *  },
	 *  data: {
	 *      description: 'bora-89 read This is an anticle article.'
	 *  },
	 *  token: '1ac20110c1363c2297444abbfb5af95509f7ef3044157513ea1b268d626dc536',
	 *  created: '2015-06-04T20:05:51.515Z',
	 *  appId: '556f0e54ee46cf4036b56239',
	 *  loadavg: [0.32080078125, 0.2548828125, 0.255859375],
	 *  freemem: 229593088,
	 *  totalmem: 1570467840,
	 *  cpus: [
	 *      [Object],
	 *      [Object],
	 *      [Object],
	 *      [Object]
	 *  ]
	 * }
     * @return [type]       [description]
     */
    public function publishEvent($data);
}