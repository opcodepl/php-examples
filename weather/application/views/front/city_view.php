<table class="table table-condensed">
	<thead>
		<tr>
			<td><strong>Lokalizacja</strong></td>
			<td><?php echo $weather['location']; ?></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><strong>Miasto</strong></td>
			<td><?php echo $weather['city']; ?></td>
		</tr>
		<tr>
			<td><strong>Czas</strong></td>
			<td><?php echo $weather['time']; ?></td>
		</tr>
		<tr>
			<td><strong>Wiatr</strong></td>
			<td><?php echo $weather['wind']; ?></td>
		</tr>
		<tr>
			<td><strong>Widoczność</strong></td>
			<td><?php echo $weather['visibility']; ?></td>
		</tr>
		<tr>
			<td><strong>Temperatura</strong></td>
			<td><?php echo $weather['temperature']; ?></td>
		</tr>
		<tr>
			<td><strong>Punkt rosy</strong></td>
			<td><?php echo $weather['dewpoint']; ?></td>
		</tr>
		<tr>
			<td><strong>Wilgotność</strong></td>
			<td><?php echo $weather['relativehumidity']; ?></td>
		</tr>
		<tr>
			<td><strong>Ciśnienie</strong></td>
			<td><?php echo $weather['pressure']; ?></td>
		</tr>
	</tbody>
</table>