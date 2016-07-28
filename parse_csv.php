function parse_csv($string, $field_delimiter = ',', $field_encapsulator = '"', $escaped_field_encapsulator = '""', $row_delimiter = "\n") {

	// establish key data
	$string_length = strlen($string);

	$field_delimiter_length = strlen($field_delimiter);
	$field_encapsulator_length = strlen($field_encapsulator);
	$escaped_field_encapsulator_length = strlen($escaped_field_encapsulator);
	$row_delimiter_length = strlen($row_delimiter);

	$escaped_field_encapsulator_contains_field_encapsulator = strpos($escaped_field_encapsulator, $field_encapsulator) === false ? false : true;

	// initialise loop variables
	$next_field_start = 0;

	$next_field_delimiter = -1;
	$next_row_delimiter = -1;

	$row = 0;
	$field = 0;

	$arr = [];

	do {
		$string_position = $next_field_start;

		// locate next field and row delimiter
		if ($next_field_delimiter < $string_position) {
			$next_field_delimiter = strpos($string, $field_delimiter, $string_position);
		}
		if ($next_row_delimiter < $string_position) {
			$next_row_delimiter = strpos($string, $row_delimiter, $string_position);
		}

		// check for encapsulated field
		if (substr($string, $string_position, $field_encapsulator_length) == $field_encapsulator) {

			$search_offset = $string_position + $field_encapsulator_length;

			$next_field_encapsulator = strpos($string, $field_encapsulator, $search_offset);

			if ($escaped_field_encapsulator_contains_field_encapsulator) {

				$next_escaped_field_encapsulator = strpos($string, $escaped_field_encapsulator, $search_offset);

				// find the next field encapsulator that is not part of an escaped field encapsulator string
				while ($next_field_encapsulator >= $next_escaped_field_encapsulator && $next_escaped_field_encapsulator !== false) {
					$search_offset = $next_escaped_field_encapsulator + $escaped_field_encapsulator_length;

					$next_field_encapsulator = strpos($string, $field_encapsulator, $search_offset);
					$next_escaped_field_encapsulator = strpos($string, $escaped_field_encapsulator, $search_offset);
				}
			}

			$search_offset = $next_field_encapsulator + $field_encapsulator_length;

			$next_field_delimiter = strpos($string, $field_delimiter, $search_offset);
			$next_row_delimiter = strpos($string, $row_delimiter, $search_offset);

			$decode_field = true;
		}
		else {
			$decode_field = false;
		}

		$this_row = $row;
		$this_field = $field;
		$field_start = $string_position;

		if (($next_field_delimiter !== false && $next_row_delimiter !== false && $next_field_delimiter < $next_row_delimiter) || ($next_row_delimiter === false && $next_field_delimiter !== false)) {
			// add a field to current row
			$field_length = $next_field_delimiter - $field_start;
			$field++;
			// prepare for next iteration
			$next_field_start = $next_field_delimiter + $field_delimiter_length;
		}
		else if ($next_row_delimiter !== false) {
			// last section of current row, but there is another row
			$field_length = $next_row_delimiter - $field_start;
			$row++;
			$field = 0;
			// prepare for next iteration
			$next_field_start = $next_row_delimiter + $row_delimiter_length;
		}
		else {
			// no more field or row delimiters, whatever we have left is the last field
			$field_length = $string_length - $string_position;
			// exit after loop completion
			$next_field_start = false;
		}

		if ($decode_field) {
			// remove encapsulation & replace escaped field encapsulators
			$arr[$this_row][$this_field] = str_replace($escaped_field_encapsulator, $field_encapsulator, substr($string, $string_position + $field_encapsulator_length, $field_length - (2 * $field_encapsulator_length)));
		}
		else {
			$arr[$this_row][$this_field] = substr($string, $string_position, $field_length);
		}

	} while ($next_field_start !== false);

	return $arr;
}
