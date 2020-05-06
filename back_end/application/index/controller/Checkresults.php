<?php

namespace app\index\controller;

use \think\Db;

class Checkresults extends BaseController
{
    /**
     * 查看查寝记录
     */
    public function checkRecords()
    {
        // 校验参数是否存在
        $parameter = array();
        $parameter = ['grade', 'department'];
        $result = $this->checkForExistence($parameter);
        if ($result) {
            return $result;
        }

        // 查询条件
        $where = array();
        $where['s.grade'] = $_POST['grade'];
        $where['s.department'] = $_POST['department'];
        $record = Db::table('record')
            ->field('start_time')   // 指定字段
            ->alias('r')    // 别名
            ->join('dorm d', 'd.id = r.dorm_id')
            ->join('student s', 's.id = d.student_id')
            ->distinct(true)   // 返回唯一不同的值
            ->where($where)
            ->where('r.deleted', 0)
            ->order('start_time desc')
            ->select();

        if ($record) {
            $return_data = array();
            $return_data['error_code'] = 0;
            $return_data['msg'] = '显示查寝记录';
            $return_data['data'] = $record;

            return json($return_data);
        } else {
            $return_data = array();
            $return_data['error_code'] = 2;
            $return_data['msg'] = '暂无查寝记录';

            return json($return_data);
        }
    }

    /**
     * 选择日期查看查寝记录
     */
    public function specifiedDate()
    {
        // 校验参数是否存在
        $parameter = array();
        $parameter = ['grade', 'department', 'date'];
        $result = $this->checkForExistence($parameter);
        if ($result) {
            return $result;
        }
        $date = $_POST['date'];

        // 判断是否非法日期
        $is_date = strtotime($date) ? strtotime($date) : false;
        if ($is_date === false && $date != "") {
            $return_data = array();
            $return_data['error_code'] = 3;
            $return_data['msg'] = '日期格式非法';

            return json($return_data);
        }

        // 查询条件
        $where = array();
        $where['s.grade'] = $_POST['grade'];
        $where['s.department'] = $_POST['department'];
        // dump($_POST['date']);
        $record = Db::table('record')
            ->field('start_time')   // 指定字段
            ->alias('r')    // 别名
            ->join('dorm d', 'd.id = r.dorm_id')
            ->join('student s', 's.id = d.student_id')
            ->distinct(true)   // 返回唯一不同的值
            ->where($where)
            ->where('start_time', 'between time', [$date. ' 00:00:00', $date . ' 23:59:59'])
            ->where('r.deleted', 0)
            ->select();

        if ($record) {
            $return_data = array();
            $return_data['error_code'] = 0;
            $return_data['msg'] = '显示' . $date . '查寝记录';
            $return_data['data'] = $record;

            return json($return_data);
        } else {
            $return_data = array();
            $return_data['error_code'] = 2;
            $return_data['msg'] = '没有' . $date . '这天查寝记录';

            return json($return_data);
        }
    }

    /**
     * 删除查寝记录
     */
    public function deleteRecord()
    {

        // 校验参数是否存在
        $parameter = array();
        $parameter = ['department', 'grade', 'start_time'];
        $result = $this->checkForExistence($parameter);
        if ($result) {
            return $result;
        }
        // 查询条件
        $where = array();
        $where['s.grade'] = $_POST['grade'];
        $where['s.department'] = $_POST['department'];
        $where['r.start_time'] = $_POST['start_time'];
        // dump($_POST['start_time']);
        $result = Db::table('record')
            ->alias('r')    // 别名
            ->join('dorm d', 'd.id = r.dorm_id')
            ->join('student s', 's.id = d.student_id')
            ->where($where)
            ->update(['record.deleted' => 1]);

        if ($result) {
            $return_data = array();
            $return_data['error_code'] = 0;
            $return_data['msg'] = '删除查寝记录' . $result . '条';
            // $return_data['data'] = $result;

            return json($return_data);
        } else {
            $return_data = array();
            $return_data['error_code'] = 2;
            $return_data['msg'] = '无此时间的查寝记录';

            return json($return_data);
        }
    }

    /**
     * 查看详细
     */
    public function viewDetails()
    {
        // 校验参数是否存在
        $parameter = array();
        $parameter = ['grade', 'department', 'start_time'];
        $result = $this->checkForExistence($parameter);
        if ($result) {
            return $result;
        }

        // 查询条件
        $where = array();
        $where['s.grade'] = $_POST['grade'];
        $where['s.department'] = $_POST['department'];
        $where['r.start_time'] = $_POST['start_time'];
        $record = Db::table('record')
            ->field('start_time, end_time, photo, d.dorm_num, r.rand_num')   // 指定字段
            ->alias('r')    // 别名
            ->join('dorm d', 'd.id = r.dorm_id')
            ->join('student s', 's.id = d.student_id')
            ->where($where)
            ->where('r.deleted', 0)
            ->select();

        if ($record) {
            $return_data = array();
            $return_data['error_code'] = 0;
            $return_data['msg'] = '显示查寝记录';
            $return_data['data'] = $record;

            return json($return_data);
        } else {
            $return_data = array();
            $return_data['error_code'] = 2;
            $return_data['msg'] = '无该天的查寝记录';

            return json($return_data);
        }
    }


    /**
     * 学生查看查寝记录
     */
    public function studentCheckRecords()
    {
        // 校验参数是否存在
        $parameter = array();
        $parameter = ['grade', 'department', 'student_id'];
        $result = $this->checkForExistence($parameter);
        if ($result) {
            return $result;
        }

        // 查询条件
        $where = array();
        $where['s.grade'] = $_POST['grade'];
        $where['s.department'] = $_POST['department'];
        $where['s.id'] = $_POST['student_id'];

        $record = Db::table('record')
            ->field('start_time')   // 指定字段
            ->alias('r')    // 别名
            ->join('dorm d', 'd.id = r.dorm_id')
            ->join('student s', 's.id = d.student_id')
            ->distinct(true)   // 返回唯一不同的值
            ->where($where)
            ->where('r.deleted', 0)
            ->order('start_time desc')
            ->select();

        if ($record) {
            $return_data = array();
            $return_data['error_code'] = 0;
            $return_data['msg'] = '显示查寝记录';
            $return_data['data'] = $record;

            return json($return_data);
        } else {
            $return_data = array();
            $return_data['error_code'] = 2;
            $return_data['msg'] = '暂无查寝记录';

            return json($return_data);
        }
    }

    /**
     * 学生查看详细
     */
    public function studentViewDetails()
    {
        // 校验参数是否存在
        $parameter = array();
        $parameter = ['grade', 'department', 'start_time', 'student_id'];
        $result = $this->checkForExistence($parameter);
        if ($result) {
            return $result;
        }

        // 查询条件
        $where = array();
        $where['s.grade'] = $_POST['grade'];
        $where['s.department'] = $_POST['department'];
        $where['r.start_time'] = $_POST['start_time'];
        $where['s.id'] = $_POST['student_id'];

        $record = Db::table('record')
            ->field('start_time, end_time, photo, d.dorm_num, r.rand_num')   // 指定字段
            ->alias('r')    // 别名
            ->join('dorm d', 'd.id = r.dorm_id')
            ->join('student s', 's.id = d.student_id')
            ->where($where)
            ->where('r.deleted', 0)
            ->select();

        if ($record) {
            $return_data = array();
            $return_data['error_code'] = 0;
            $return_data['msg'] = '显示查寝记录';
            $return_data['data'] = $record;

            return json($return_data);
        } else {
            $return_data = array();
            $return_data['error_code'] = 2;
            $return_data['msg'] = '无该天的查寝记录';

            return json($return_data);
        }
    }
}