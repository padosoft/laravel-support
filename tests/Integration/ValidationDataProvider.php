<?php

namespace Padosoft\Laravel\Support\Test\Integration;


trait ValidationDataProvider
{
    /**
     * @return array
     */
    public static function validateProvider()
    {
        return [
            'null, null ' => [null, null, 'ErrorException'],
            '\'\', null ' => ['', null, 'ErrorException'],
            'null, \'\' ' => [null, '', true],
            '\'\', \'\'' => ['', '', true],
            '\'\', \' \'' => ['', ' ', true],
            '\' \', \'\'' => [' ', '', true],
            '\' \', \' \'' => [' ', ' ', true],
            '20150230, date' => ['20150230', 'date', false],
            '20150227, date' => ['20150227', 'date', true],
            '192.168.10.10, ip' => ['192.168.10.10', 'ip', true],
            '[192.168.10.10, 192.168.10.11], ip' => [['192.168.10.10','192.168.10.11'], 'ip', true],
            '[192.168.10.10, dasdsadad], ip' => [['192.168.10.10','dasdsadad'], 'ip', true],
            '[dasdsadad, 192.168.10.10], ip' => [['dasdsadad', '192.168.10.10'], ['ip','ip'], false],
        ];
    }
}
