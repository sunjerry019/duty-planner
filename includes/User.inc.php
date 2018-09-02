<?php

	class User
	{
		private $dbID, $displayName, $username, $passwordHash, $usertype, $startDate, $endDate;
		public $pointPerDay, $desirabilityIndex;
		
		public function __construct($targetUser) //autoload from DB based on username or dbID
		{
			try
			{
				$dbh = new PDO(DB_PATH);
			}
			catch(PDOException $e)
			{
				throw $e;
			}

			if(is_int($targetUser)) //targetUser is specified in db ID
			{
				$query = $dbh->prepare("SELECT * FROM users WHERE id=?");
				$query->execute(array($targetUser));
				$row = $query->fetchAll(PDO::FETCH_ASSOC);
				if(count($row) < 1) throw new Exception("No user with database id ".$targetUser." was found");
				$this->dbID = $targetUser;
				$this->displayName = $row[0]["displayName"];
				$this->username = $row[0]["username"];
				$this->passwordHash = $row[0]["password"];
				$this->startDate = new DateTime($row[0]["dutyStartDate"]);
				$this->endDate = new DateTime($row[0]["dutyEndDate"]);
				$this->usertype = (int)$row[0]["userType"];
			}
			else //targetUser specified as username
			{
				$query = $dbh->prepare("SELECT * FROM users WHERE username=?");
				$query->execute(array(strtolower($targetUser)));
				$row = $query->fetchAll(PDO::FETCH_ASSOC);
				if(count($row) < 1) throw new Exception("Username '".$targetUser."' was not found in database");
				$this->dbID = (int)$row[0]["id"];
				$this->displayName = $row[0]["displayName"];
				$this->username = $targetUser;
				$this->passwordHash = $row[0]["password"];
				$this->startDate = new DateTime($row[0]["dutyStartDate"]);
				$this->endDate = new DateTime($row[0]["dutyEndDate"]);
				$this->usertype = (int)$row[0]["userType"];
			}
			$dbh = null;
		}

		public function verifyPassword($plainTextPassword)
		{
			return password_hash($plainTextPassword, PASSWORD_DEFAULT) === $passwordHash;
		}

		public function isDutyEligible($date)
		{
			$dateTime = new DateTime($date);
			
			//first check it is bounded within startDate and endDate
			if($dateTime < $this->startDate || $dateTime > $this->endDate) return false;

			//then check for this guy's exemptions
			try
			{
				$dbh = new PDO(DB_PATH);
			}
			catch(PDOException $e)
			{
				throw $e;
			}
			$query = $dbh->prepare("SELECT startDate, endDate, type FROM exemptions WHERE user=?");
			$query->execute(array($this->dbID));
			$rows = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach($rows as $row)
			{
				$start = new DateTime($row["startDate"]);
				$end = new DateTime($row["endDate"]);
				
				//no matter the type if it is bounded inside the exemption date confirm not eligible
				if($start <= $dateTime && $dateTime <= $end) return false;

				// types of exemptions:
				// 1 - off/leave (no duty on the weekend[s] the offs/leaves touches if more than 4 consecutive days)
				// 2 - MC (no duty on the first day after MC)
				// 3 - MA/others (no special treatment)

				//check for the special cases
				if($row["type"] == 1)
				{
					//check 4 or more consecutive days
					$interval = (int)$start->diff($end)->format("%a");
					if($interval >= 3)
					{
						//extends start/end date if weekend[s] touched
						$startDay = (int)$start->format("N");
						$endDay = (int)$end->format("N");
						if($startDay === 1)
						{
							$start->sub(new DateInterval("P2D"));
						}
						else if($startDay === 7 && $interval != 3)
						{
							$start->sub(new DateInterval("P1D"));
						}
						if($endDay === 5)
						{
							$end->add(new DateInterval("P2D"));
						}
						else if($endDay === 6 && $interval != 3)
						{
							$end->add(new DateInterval("P1D"));
						}
						if($start <= $dateTime && $dateTime <= $end) return false;
					}
				}
				if($row["type"] == 2)
				{
					$end->add(new DateInterval("P1D"));
					if($start <= $dateTime && $dateTime <= $end) return false;
				}
			}
			$dbh = null;
			return true;
		}

		public function isNewlyPostedIn()
		{
			try
			{
				$dbh = new PDO(DB_PATH);
			}
			catch(PDOException $e)
			{
				throw $e;
			}
			$query = $dbh->prepare("SELECT COUNT(*) FROM dutyPersonnel WHERE userOnDuty=?");
			$query->execute(array($this->dbID));
			$row = $query->fetch(PDO::FETCH_NUM);
			$dbh = null;
			if((int)$row[0] >= 2) return false;
			else return true;
		}

		public function calculatePointPerDay($date)
		{
			if(isset($this->pointPerDay)) return $this->pointPerDay;
			
			//first calculate total points
			$totalPoints = 0;
			try
			{
				$dbh = new PDO(DB_PATH);
			}
			catch(PDOException $e)
			{
				throw $e;
			}
			$query = $dbh->prepare("SELECT date FROM dutyPersonnel WHERE userOnDuty=?");
			$query->execute(array($this->dbID));
			$rows = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach($rows as $row)
			{
				$dutyDate = new DateTime($row["date"]);
				$dayOfWeek = (int)$dutyDate->format("N");
				if($dayOfWeek <= 4)
				{
					$totalPoints += WEEKDAY_POINTS;
				}
				else if($dayOfWeek === 5)
				{
					$totalPoints += FRIDAY_POINTS;
				}
				else
				{
					$totalPoints += WEEKEND_POINTS;
				}
			}

			//now calculate number of duty eligible days
			$day = new DateTime($date);
			$dutyEligibleDays = (int)$this->startDate->diff($day)->format("%a"); //get total first then subtract from there
			$query = $dbh->prepare("SELECT startDate, endDate, type FROM exemptions WHERE user=?");
			$query->execute(array($this->dbID));
			$rows = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach($rows as $row)
			{
				$start = new DateTime($row["startDate"]);
				$end = new DateTime($row["endDate"]);

				// types of exemptions:
				// 1 - off/leave (no duty on the weekend[s] the offs/leaves touches if more than 4 consecutive days)
				// 2 - MC (no duty on the first day after MC)
				// 3 - MA/others (no special treatment)

				$interval = (int)$start->diff($end)->format("%a");
				$dutyEligibleDays -= ($interval+1);

				//check for the special cases
				if($row["type"] == 1)
				{
					//check 4 or more consecutive days
					if($interval >= 3)
					{
						//extends start/end date if weekend[s] touched
						$startDay = (int)$start->format("N");
						$endDay = (int)$end->format("N");
						if($startDay === 1)
						{
							$dutyEligibleDays -= 2;
						}
						else if($startDay === 7 && $interval != 3)
						{
							$dutyEligibleDays -= 1;
						}
						if($endDay === 5)
						{
							$dutyEligibleDays -= 2;
						}
						else if($endDay === 6 && $interval != 3)
						{
							$dutyEligibleDays -= 1;
						}
					}
				}
				if($row["type"] == 2)
				{
					$dutyEligibleDays -= 1;
				}
			}
			if($dutyEligibleDays < 1) throw new Exception("User ".$this->displayName." is newly posted in, cannot calculate points per duty eligible day!");
			$this->pointPerDay = $totalPoints/$dutyEligibleDays;
			return $this->pointPerDay;
		}

		public function getDisplayName()
		{
			return $this->displayName;
		}

		public function getDbID()
		{
			return $this->dbID;
		}

		static function comparePointPerDay($a, $b)
		{
			if($a->pointPerDay == $b->pointPerDay)
				return 0;
			return ($a->pointPerDay > $b->pointPerDay) ? 1 : -1;
		}
	}
	
?>
