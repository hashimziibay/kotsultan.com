import 'package:flutter_test/flutter_test.dart';
import 'package:kotsultan_app/main.dart';

void main() {
  testWidgets('KotSultanApp builds', (WidgetTester tester) async {
    await tester.pumpWidget(const KotSultanApp());
    // First frame shows bootstrap spinner before SharedPreferences resolves.
    expect(find.byType(KotSultanApp), findsOneWidget);
  });
}
