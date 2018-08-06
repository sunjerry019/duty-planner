<?php

    class Day
    {
        private $date, $dutyPersonnel, $isManual, $isSet;
        
        public function __construct()
        {
            if(func_num_args() === 1)
            {
                $this->date = func_get_arg(0);
            }
            else
            {
                $this->date = date("Y-m-d");
            }

            try
			{
				$dbh = new PDO(DB_PATH);
			}
			catch(PDOException $e)
			{
				throw $e;
            }
            $query = $dbh->prepare("SELECT * FROM dutyPersonnel WHERE date=?");
            $query->execute(array($this->date));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            if(count($row) < 1)
            {
                $this->isSet = false;
            }
            else
            {
                $this->dutyPersonnel = new User((int)$row[0]["userOnDuty"]);
                $this->isManual = ($row[0]["isManual"] === 1);
                $this->isSet = true;
            }
            $dbh = null;
        }

        public function setDutyPersonnel()
        {
            if($this->isManual || $this->isSet) return $this->dutyPersonnel;
            $pool = new DutyPool($this->date);

            $this->dutyPersonnel = $pool->selectDutyPersonnel();
            try
			{
                $dbh = new PDO(DB_PATH);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch(PDOException $e)
			{
				throw $e;
            }
            $dbh->beginTransaction();
            if($this->isSet)
            {
                $query = $dbh->prepare("UPDATE dutyPersonnel SET userOnDuty=?, isManual=0 WHERE date=?");
                $query->execute(array($this->dutyPersonnel->getDbID(),$this->date));
            }
            else
            {
                $query = $dbh->prepare("INSERT INTO dutyPersonnel (userOnDuty, date, isManual) VALUES (?,?,0)");
                $query->execute(array($this->dutyPersonnel->getDbID(), $this->date));
                $this->isSet = true;
                $this->isManual = false;
            }
            $dbh->commit();
            $dbh = null;
            return $this->dutyPersonnel;
        }

        public function getDutyPersonnel()
        {
            if(!$this->isSet) return;
            return $this->dutyPersonnel;
        }
    }

?>