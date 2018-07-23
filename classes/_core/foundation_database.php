<?php
	//
	// database.php
	//
	/**
	*
	* This code is created and owned by Char-Lez Braden.
	*
	* It is licensed to the Dave Nolan Society, free of charge, in perpetuity
	* for any purpose, including revenue generating purposes.
	* It may be copied, modified, published, transfered and distrbuted.
	* Modifications and derivative works become the property of Char-Lez Braden.
	* Modifications and derivative works are also licensed to the Dave Nolan Society
	* free of charge, in perpetuity for any purpse including revenue generating purposes.
	* Char-Lez Braden, his heirs, successors and appointees may use this code, and its
	* modified or derivative works for any purpose what so ever and no part of this license
	* shall be construed as an exclusive license to the Dave Nolan Society.
	*
	* @author		Char-Lez Braden
  * @version	1.0
  * @since		2018-06-11
	*/
	class database
	{
		private $connection;
		private $db;
		private $row;
		private $select;
		private $SQL;
		//
		public function __construct($configuration)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 1);
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_array($configuration);
				//
				//
				//////////////////
				// Sanity Check //
				//////////////////
				//
				confirm_array_element('database_host', $configuration);
				confirm_array_element('database_user', $configuration);
				confirm_array_element('database_password', $configuration);
				confirm_array_element('database_name', $configuration);
				//
				//
				/////////////////////////////
				// Connect to the database //
				/////////////////////////////
				//
				$this->connection=@mysqli_connect($configuration['database_host'], $configuration['database_user'], $configuration['database_password']);
				if ($this->connection===FALSE)
				{
					throw new foundation_fault('Could not connect to database', mysqli_error());
				} // if ($this->connection===FALSE)
				//
				$this->db=@mysqli_select_db($this->connection, $configuration['database_name']);
				if ($this->db===FALSE)
				{
					throw new foundation_fault('Could not select database', mysqli_error());
				} // if ($this->db===FALSE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not create database', '', $e);
			} // try
		} // __construct()
		//
		//
		public function close()
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 0);
				//
				//
				$close=@mysqli_close($this->connection);
				//
				if ($close===FALSE)
				{
					throw new foundation_fault('Close failed', mysqli_error($this->connection));
				} // if ($close===FALSE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not close database', '', $e);
			} //try
		} // close()
		//
		//
		public function fetch()
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 0);
				//
				//
				/////////////////
				// Fetch a row //
				/////////////////
				//
				$this->row=@mysqli_fetch_assoc($this->select);
				//
				return $this->row;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not get row', '', $e);
			} // try
		} // fetch()
		//
		//
		public function row_count()
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 0);
				//
				//
				//////////////////////////
				// Return the row count //
				//////////////////////////
				//
				return @mysqli_num_rows($this->select);
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not count rows', '', $e);
			} // try
		} // row_count()
		//
		//
		public function query($SQL, $tokens)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				switch ($arg_count)
				{
					case 1: {
						$SQL=func_get_arg(0);
						$tokens=array();
					break; }
					//
					case 2: {
						$SQL=func_get_arg(0);
						$tokens=func_get_arg(1);
					break; }
					//
					default: {
						throw new foundation_fault("Invalid args [$arg_count]", origin());
					break; }
				} // switch ($arg_count)
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_string($SQL);
				confirm_array($tokens);
				//
				//
				foreach ($tokens as $id=>$value)
				{
					if ((is_string($value)===FALSE) && (is_numeric($value)===FALSE))
					{
						throw new foundation_fault("Invalid value type [$id]=>[".gettype($value).']', origin());
					} // ((is_string($value)===FALSE) && (is_numeric($value)===FALSE))
				} // foreach ($tokens as $id=>$value)
				//
				//
				///////////////////////
				// Process the query //
				///////////////////////
				//
				foreach ($tokens as $id=>$value)
				{
					$target="#{$id}#";
					$safe=@mysqli_real_escape_string($this->connection, $value);
					//
					$SQL=str_replace($target, $safe, $SQL);
				} // for ($a=0; $a<$arg_count; $a++)
				//
				$this->SQL=$SQL;
				//
				$this->select=@mysqli_query($this->connection, $this->SQL);
				if ($this->select===FALSE)
				{
					throw new foundation_fault ('Could not do query', $this->SQL);
				} // if ($this->select===FALSE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not query database', '', $e);
			} // try
		} // query()
	} // database
?>